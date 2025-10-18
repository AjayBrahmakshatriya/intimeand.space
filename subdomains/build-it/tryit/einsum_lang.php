#include "builder/dyn_var.h"
#include "builder/static_var.h"
#include "blocks/extract_cuda.h"
#include "blocks/c_code_generator.h"
#include "blocks/rce.h"
#include &lt;vector&gt;

#define CTA_SIZE (512)
// Complete implementation of the Einsum Lang compiler that
// generates CPU and GPU code for expressions like A[i] = B[i][j]...
// Expand to read and change the implementation. 
namespace el {

// Implementation of the index type
struct index {
	// While in use, the iterator to use
	builder::dyn_var&lt;int&gt; m_iterator = 0;
	int m_index_bound = 0;	
};

// Type to hold expressions likes T[x]..
template&lt;typename T&gt;
struct tensor_access;

// Implementation for the index type
template &lt;typename T&gt;
struct tensor {
	int m_dims;

	// Statically known tensor sizes
	std::vector&lt;int&gt; m_sizes;

	// Static tracking for constant values
	builder::static_var&lt;int&gt; is_constant = false;
	builder::static_var&lt;T&gt; constant_val = 0;
	
	// Underlying data buffer
	builder::dyn_var&lt;T*&gt; m_buffer;

	tensor(const std::vector&lt;int&gt;&amp; sizes, const builder::dyn_var&lt;T*&gt;&amp; buffer):
		m_dims(sizes.size()), m_sizes(std::move(sizes)), m_buffer(buffer) {
	}

	// Operator to create a tensor_access from tensor
	tensor_access&lt;T&gt; operator [] (index &amp;i);
	
};

// Base class for tensor access of any type
struct tensor_access_base {
	virtual builder::builder get_value() const { return 0;}
	virtual ~tensor_access_base() = default;
	virtual std::vector&lt;index*&gt; get_indices() const {return std::vector&lt;index*&gt;(); }
};

template &lt;typename T&gt;
struct tensor_access;

struct rhs_terms {
	enum term_type {
		tensor_access,
		product,
		sum,
		constant,
	};

	enum term_type m_type;	
	const tensor_access_base* m_tab;
	const rhs_terms* m_term1;
	const rhs_terms* m_term2;
	std::shared_ptr&lt;builder::dyn_var&lt;int&gt;&gt; m_constant;

	rhs_terms() {}
	
	template &lt;typename T&gt;
	rhs_terms(const struct tensor_access&lt;T&gt;&amp; t);

	rhs_terms(const int &amp;x): rhs_terms(builder::dyn_var&lt;int&gt; (x)) {}
	
	template &lt;typename T&gt;	
	rhs_terms(const builder::dyn_var&lt;T&gt; &amp;b) {
		m_type = constant;
		m_constant = std::make_shared&lt;builder::dyn_var&lt;int&gt;&gt;(b);
	}

	builder::builder get_value(void) const {
		switch(m_type) {
			case tensor_access: return m_tab-&gt;get_value();
			case product: return m_term1-&gt;get_value() * m_term2-&gt;get_value();
			case sum: return m_term1-&gt;get_value() + m_term2-&gt;get_value();
			case constant: return *m_constant;
		}
		return 0;
	}
	void get_indices(std::vector&lt;index*&gt; &amp;set) const {
		if (m_type == product || m_type == sum) {
			m_term1-&gt;get_indices(set);
			m_term2-&gt;get_indices(set);
			return;
		}		
		if (m_type == tensor_access) {
			auto indices = m_tab-&gt;get_indices();
			for (auto a: indices) {
				if (std::find(set.begin(), set.end(), a) == set.end()) {
					set.push_back(a);
				}	
			}	
			return;
		}
		return;
	}
};

// Enums for tracking current device // Scheduling
enum device_type {
	SERIAL = 0,
	CPU_PARALLEL = 1,
	GPU_PARALLEL = 2
};
enum device_type current_device = SERIAL;


static std::vector&lt;index*&gt; get_reduce_indices(std::vector&lt;index*&gt; lhs_set, const rhs_terms&amp; rhs) {
	// Next we will gather indices that are used on the right, but not on the left
	std::vector&lt;index*&gt; rhs_set;
	std::vector&lt;index*&gt; all_rhs_set;
	rhs.get_indices(all_rhs_set);
	for (auto x: all_rhs_set) {
		if (std::find(lhs_set.begin(), lhs_set.end(), x) == lhs_set.end()) {
			if (std::find(rhs_set.begin(), rhs_set.end(), x) == rhs_set.end()) {
				rhs_set.push_back(x);
			}
		}
	}
	return rhs_set;
}

// Tensor access implementation
template &lt;typename T&gt;
struct tensor_access: public tensor_access_base {
	tensor&lt;T&gt;&amp; m_tensor;
	std::vector&lt;index*&gt; m_indices;

	tensor_access(tensor&lt;T&gt;&amp; _t): m_tensor(_t) {}

	// Operator for multiple indices chaining
	tensor_access&lt;T&gt; operator [] (index &amp;i);	


	void create_increment(const rhs_terms &amp;rhs, std::vector&lt;index*&gt; reduce_indices, builder::dyn_var&lt;int&gt;&amp; buffer_index) {
		if (reduce_indices.size())
			m_tensor.m_buffer[buffer_index] = m_tensor.m_buffer[buffer_index] + rhs.get_value();
		else 
			m_tensor.m_buffer[buffer_index] = rhs.get_value();
	}	

	builder::dyn_var&lt;int&gt; create_index(int idx) {
		if (idx == 0)
			return (m_indices[0]-&gt;m_iterator);
		return create_index(idx - 1) * (int) (m_tensor.m_sizes[idx]) + (m_indices[idx]-&gt;m_iterator);
	}

	void create_assign(const rhs_terms &amp;rhs, std::vector&lt;index*&gt; reduce_indices) {
		builder::dyn_var&lt;int&gt; v = create_index(m_tensor.m_dims-1);
		if (reduce_indices.size())
			m_tensor.m_buffer[v] = 0;
		induce_reduce_loop(0, rhs, reduce_indices, v);	
	}

	
	// Functions for create loops on the RHS
	void induce_reduce_loop(int idx, const rhs_terms &amp;rhs, std::vector&lt;index*&gt; reduce_indices, 
		builder::dyn_var&lt;int&gt;&amp; buffer_index) {
		if (idx == (int)reduce_indices.size()) {
			create_increment(rhs, reduce_indices, buffer_index);
			return;
		}
		// Now add a new loop for a reduce index	
		builder::dyn_var&lt;int&gt; &amp;iter = reduce_indices[idx]-&gt;m_iterator;
		for (iter = 0; iter &lt; reduce_indices[idx]-&gt;m_index_bound; iter = iter + 1) {
			induce_reduce_loop(idx + 1, rhs, reduce_indices, buffer_index);
		}
	}	
	
	// Functions to create loops on the LHS
	void induce_loops(int idx, const rhs_terms&amp; rhs, std::vector&lt;index*&gt; reduce_indices) {
		if (idx == m_tensor.m_dims) {
			create_assign(rhs, reduce_indices);
			return;
		} 	
		if (idx == 0 &amp;&amp; current_device == GPU_PARALLEL) {
			int num_cta = (m_tensor.m_sizes[idx] + CTA_SIZE - 1) / CTA_SIZE;
			builder::annotate(CUDA_KERNEL);
			for (builder::dyn_var&lt;int&gt; cta = 0; cta &lt; num_cta; cta = cta + 1) {
				for (builder::dyn_var&lt;int&gt; tid = 0; tid &lt; CTA_SIZE; tid = tid + 1) {
					builder::dyn_var&lt;int&gt; thread = cta * CTA_SIZE + tid;
					if ((m_tensor.m_sizes[idx] % CTA_SIZE == 0) || (bool)(thread &lt; m_tensor.m_sizes[idx])) {
						m_indices[idx]-&gt;m_iterator = thread;
						induce_loops(idx + 1, rhs, reduce_indices);	
					}
				}
			}
		} else {
			// Implement a level of loop and recurse	
			if (idx == 0 &amp;&amp; current_device == CPU_PARALLEL) {
				builder::annotate("pragma: omp parallel for");
			}
			builder::dyn_var&lt;int&gt; &amp;iter = m_indices[idx]-&gt;m_iterator;
			for (iter = 0; iter &lt; m_tensor.m_sizes[idx]; iter = iter + 1) {
				induce_loops(idx + 1, rhs, reduce_indices);	
			}
		}
	}
		
	// Operator over load for = 
	void operator= (const rhs_terms &amp;rhs) {
		// First we will assert that we have all the indices we need 
		assert(m_indices.size() == (size_t)(m_tensor.m_dims) &amp;&amp; "Not enough indices supplied for definition");
		std::vector&lt;index*&gt; reduce_indices = get_reduce_indices(m_indices, rhs);	
		induce_loops(0, rhs, reduce_indices);	
		m_tensor.is_constant = false;
		m_tensor.constant_val = 0;
	}

	template&lt;typename T2&gt;
	void operator = (const tensor_access&lt;T2&gt; &amp;a) {
		*this = std::move((rhs_terms)a);
	}
	void operator = (const tensor_access&lt;T&gt; &amp;a) {
		*this = std::move((rhs_terms)a);
	}
	void operator = (const T&amp; x) {
		*this = ((rhs_terms)(builder::dyn_var&lt;T&gt;)x);
		m_tensor.is_constant = true;
		m_tensor.constant_val = x;
	}	

	builder::dyn_var&lt;int&gt; create_index(int idx) const {
		if (idx == 0)
			return (m_indices[0]-&gt;m_iterator);
		return create_index(idx - 1) * (int) (m_tensor.m_sizes[idx]) + (m_indices[idx]-&gt;m_iterator);
	}
	builder::builder get_value() const override {	
		if (m_tensor.is_constant) 
			return m_tensor.constant_val;
		builder::dyn_var&lt;int&gt; v = create_index(m_tensor.m_dims - 1);	
		return m_tensor.m_buffer[v];
	}
	std::vector&lt;index*&gt; get_indices() const override {
		for (unsigned int i = 0; i &lt; m_indices.size(); i++) {
			m_indices[i]-&gt;m_index_bound = m_tensor.m_sizes[i];
		}
		return m_indices;
	}
};


template &lt;typename T&gt;
rhs_terms::rhs_terms(const struct tensor_access&lt;T&gt;&amp; t) {
	m_type = tensor_access;
	m_tab = &amp;t;
}	

// Operator overload for tensor types
template &lt;typename T&gt;
tensor_access&lt;T&gt; tensor&lt;T&gt;::operator [] (index &amp;i) {
	tensor_access&lt;T&gt; t (*this);
	t.m_indices.push_back(&amp;i);	
	return t;
}

template &lt;typename T&gt;
tensor_access&lt;T&gt; tensor_access&lt;T&gt;::operator [] (index &amp;i) {
	tensor_access&lt;T&gt; t(m_tensor);
	// We can't use this tensor access anymore after this
	t.m_indices = std::move(m_indices);
	t.m_indices.push_back(&amp;i);
	return t;
}

// Operator overloads for RHS expressions
rhs_terms operator* (const rhs_terms &amp;a, const rhs_terms &amp;b);
rhs_terms operator* (const rhs_terms &amp;a, const rhs_terms &amp;b) {
	rhs_terms new_terms;
	new_terms.m_type = rhs_terms::product;
	new_terms.m_term1 = &amp;a;
	new_terms.m_term2 = &amp;b;
	
	return new_terms;
}

rhs_terms operator+ (const rhs_terms &amp;a, const rhs_terms &amp;b);
rhs_terms operator+ (const rhs_terms &amp;a, const rhs_terms &amp;b) {
	rhs_terms new_terms;
	new_terms.m_type = rhs_terms::sum;
	new_terms.m_term1 = &amp;a;
	new_terms.m_term2 = &amp;b;
	
	return new_terms;
}

class einsum_code_generator: public block::c_code_generator {
	using block::c_code_generator::visit;
	using block::c_code_generator::c_code_generator;
	virtual void visit(block::for_stmt::Ptr);
public:
	static void generate_code(block::block::Ptr ast, std::ostream &amp;oss, int indent = 0) {
		einsum_code_generator generator(oss);
		generator.curr_indent = indent;
		ast-&gt;accept(&amp;generator);
		oss &lt;&lt; std::endl;
	}
};

static void run_einsum_pipeline(block::block::Ptr ast, std::ostream &amp;oss) {
	// Run a preprocessing pass to eliminate all redundant variables
	block::eliminate_redundant_vars(ast);	
	auto new_decls = block::extract_cuda_from(block::to&lt;block::func_decl&gt;(ast)-&gt;body);
	for (auto a: new_decls)
		block::c_code_generator::generate_code(a, oss, 0);
	einsum_code_generator::generate_code(ast, oss, 0);
}

void einsum_code_generator::visit(block::for_stmt::Ptr s) {
	std::string pragma_prefix ("pragma: ");
	for (auto a: s-&gt;annotation) {
		if (!a.compare(0, pragma_prefix.size(), pragma_prefix)) {
			std::string pragma_value = a.substr(pragma_prefix.size());
			oss &lt;&lt; "_Pragma(\"" &lt;&lt; pragma_value &lt;&lt; "\")" &lt;&lt; std::endl;
			printer::indent(oss, curr_indent);
		}
	}
	block::c_code_generator::visit(s);
}



}

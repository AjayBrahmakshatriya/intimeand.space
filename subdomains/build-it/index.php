<?php $ogtitle="BuildIt"; ?>
<?php $ogimg="https://intimeand.space/images/logo-buildit.png"; ?>
<?php include 'params.php' ?>
<?php include '../../template.php'; ?>
<?php include 'buildit-template.php'; ?>
<script>document.getElementById("link-home").classList.add("active");</script>
<style>
article {
	max-width: 80em;
}
table {
	width: 100%;
	border-spacing: 0;
}
tr { 
	width: 50%;
	margin-bottom: 4em;
}
td {
	vertical-align: top;
	width: 50%;
	padding: 2em;
}
.code-block {
	/*width: 30em;*/
	background-color: white;	
	display: inline-block;
	white-space: nowrap;
	box-shadow: rgba(0, 0, 0, 0.3) 2px 2px 8px;
}
.code-keyword {
	color: blue;
}
.panel {
	width: 100%;
	clear: both;
	overflow: auto;
	padding: 0px;
	margin-top: 1em;
}
.panel-wheat {
	background-color: wheat;
}
.panel-left {
	float: left;
	margin: 1em;
}
.panel-right {
	float: right;
	margin: 1em;
}


.project-panel {
	width: 100%;
	display: flex;
	flex-wrap: wrap;
	justify-content: center;
}
.project {
	text-align: center;
	min-height: 16em;
	width: 16em;	
	display: inline-block;
	background-color: white;
	background-color: #f1f0eb;
	box-shadow: rgba(0, 0, 0, 0.3) 2px 2px 8px;
	padding: 1em;
	margin: 2em;
}	
.project:hover {
	/*transform: scale(1.01);*/
}
.conf-video {
	width: 560px;
	height: 400px;
}

@media screen and (max-width: 45em) {
.panel-left, .panel-right {
	width: calc(100% - 2em);
	overflow-x: auto;
}
.panel-left::-webkit-scrollbar {
	display: block;
}
.panel-right::-webkit-scrollbar {
	display: block;
}
.panel {
	background-color: wheat;
}
.conf-video {
	width: 100%;
	
}
.code-block {
	box-shadow: none;
}

}
</style>
<center>
<br>
<img src="https://intimeand.space/images/logo-buildit.png?v=<?php echo datestr('images/logo-buildit.png');?>" height="200"/>
<br>
<a class="icons-link" target="_blank" href="https://github.com/BuildIt-lang/buildit"><img src="https://img.shields.io/badge/BuildIt-MIT-brightgreen"/></a>&nbsp;<a class="icons-link" target="_blank" href="https://github.com/BuildIt-lang/buildit/fork"><img src="https://img.shields.io/github/forks/BuildIt-lang/buildit"/></a>&nbsp;<a class="icons-link" href="https://github.com/BuildIt-lang/buildit/" target="_blank"><img src="https://img.shields.io/github/stars/BuildIt-lang/buildit"/></a><br>
<a href="https://github.com/BuildIt-lang/buildit/actions/workflows/ci-all-samples.yml" class="icons-link" target="_blank"><img src="https://github.com/BuildIt-lang/buildit/actions/workflows/ci-all-samples.yml/badge.svg"/></a>
<!--<h3>&quot;Every problem in Computer Science can be solved with a level of indirection and every problem in Software Engineering can be solved with a step of code-generation&quot;</h3><br>-->
</center>

<h2 ><b>BuildIt</b>: A framework for rapidly developing high-performance Domain Specific Languages (DSLs) with little to 
no compiler knowledge. </h2>
<br>
<br>
<h2 style="text-align: center">What can you use BuildIt for?</h2> 

<div class="project-panel">
	<div class="project">
		<img src="https://intimeand.space/images/net-blocks.png" width="150" height="150"/><br><br>
		<b>Net-Blocks</b><br>
		<span >Performant, customizable and modular network stack to specialize protocols and implementations</span>
		<br>
		<a href="https://github.com/BuildIt-lang/net-blocks">GitHub</a>
	</div>
	<div class="project">
		<img src="https://intimeand.space/images/network.png" width="150" height="150"/><br><br>
		<b>Easy-GraphIt</b><br>
		<span >A 2021 LoC GraphIt compiler that generates high-performance GPU code!</span>
		<br>
		<a href="https://github.com/BuildIt-lang/easy_graphit">GitHub</a>
	</div>
	<div class="project">
		<img src="https://intimeand.space/images/summation.png" width="150" height="150"/><br><br>
		<b>Einsum-Lang</b><br>
		<span >Compiler for Einsum-expressions on N-dimensional dense tensors in 300 LoC (CPU and GPU parallel) </span>
		<br>
		<a href="/tryit?sample=einsum">Try it!</a>
	</div>
	
</div>

<br>
<hr>
<br>
<h2 style="text-align: center">BuildIt is easy to use!</h2> 

<div class="panel">
	<div class="panel-right">
		<div class="code-block" >
		#<span class="code-keyword">include</span> &lt;builder/dyn_var.h&gt;<br>
		#<span class="code-keyword">include</span> &lt;builder/static_var.h&gt;<br>
		...
		<hr>
		&gt; g++ foo.cpp -lbuildit -I buildit/include/
		</div>
	</div>
	<div class="panel-left">
		1. A portable light-weight multi-stage library that can be used with any standard C++ compiler.
	</div>

</div>

<div class="panel panel-wheat">
	<div class="panel-left">
		<div class="code-block" >
		// Code written with dyn_var&lt;T&gt;<br>
		<span class="code-keyword">for</span> (dyn_var&lt;<span class="code-keyword">int</span>&gt; i = 0; i &lt; 512; i = i + 1) {<br>
		&nbsp;&nbsp;A[i] = 0;<br>
		}<br>
		...
		<hr>
		// Generates the same code<br>
		<span class="code-keyword">for</span> (<span class="code-keyword">int</span> var1 = 0; var1 &lt; 512; var1 = var1 + 1) { <br>
		&nbsp;&nbsp;var0[var1] = 0;<br>
		}
		</div>
	</div>
	<div class="panel-right" style="max-width: 40em">
		2. Write expressions and statements with <span style="font-family: monospace">dyn_var&lt;T&gt;</span> variables to generate the same code. Easily port a high-performance library into a compiler by changing types of variables. 
	</div>	
</div>
		
<div class="panel">
	<div class="panel-right">
		<div class="code-block" >
		static_var&lt;<span class="code-keyword">int</span>&gt; bound, block_size;<br>
		...<br>
		// Splitting of loop with known bounds<br>
		<span class="code-keyword">for</span> (dyn_var&lt;<span class="code-keyword">int</span>&gt; i0 = 0; i0 &lt; bound / block_size; i0 = i0 + 1) {<br>
		&nbsp;&nbsp;<span class="code-keyword">for</span> (dyn_var&lt;<span class="code-keyword">int</span>&gt; i1 = i0 * block_size; i1 &lt; (i0 + 1) * block_size; i1 = i1 + 1) {<br>
		&nbsp;&nbsp;&nbsp;&nbsp;...<br>
		&nbsp;&nbsp;}<br>
		}<br>
		// conditionally generate padding loops<br>
		<span class="code-keyword">if</span> (bound % block_size != 0) {<br>
		&nbsp;&nbsp;<span class="code-keyword">for</span> (dyn_var<<span class="code-keyword">int</span>> i1 = (bound / block_size) * block_size; i1 < bound; i1 = i1 + 1) {<br>
		&nbsp;&nbsp;&nbsp;&nbsp;...<br>
		&nbsp;&nbsp;}<br>
		}
		</div>
	</div>
	<div class="panel-left" style="max-width: 25em">
		3. Use conditions and expressions on <span style="font-family: monospace">static_var&lt;T&gt;</span> to specialize generated code for high-performance.
	</div>
		
</div>

<div class="panel panel-wheat">
	<div class="panel-left">
		<div class="code-block" >
		builder::annotate("pragma: omp parallel for");<br>
		<span class="code-keyword">for</span> (dyn_var&lt;<span class="code-keyword">int</span>&gt; i = 0; i &lt; 512; i = i + 1) {<br>
		&nbsp;&nbsp;A[i] = 0;<br>
		}<br>
		</div>
	</div>	
	<div class="panel-right">
		4. Supports generation of parallel code for CPUs and GPUs with simple annotations	
	</div>
</div>

<br>
<hr>
<br>

<a name="conference-videos"></a>
<h2>Selected Presentations</h2>
<br>

<center>
<!--<iframe class="conf-video" width="560" height="400" src="https://www.youtube.com/embed/u31NOIKlfP0" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>&nbsp;&nbsp;-->
<iframe class="conf-video" width="560" height="400" src="https://www.youtube.com/embed/UuMprTQNxzA" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>&nbsp;&nbsp;
<iframe class="conf-video" width="560" height="400" src="https://www.youtube.com/embed/UOYiIRujoSY" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
</center>
<br>
<center>
<a href="https://www.flaticon.com/free-icons/code" title="code icons">Code icons created by Maxim Basinski Premium - Flaticon</a>
</center>



<?php include '../../footer.php'; ?>

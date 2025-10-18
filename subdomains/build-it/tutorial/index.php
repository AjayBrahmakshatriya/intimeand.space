<?php $ogtitle="BuildIt"; ?>
<?php include '../params.php'; ?>
<?php include '../../../template.php'; ?>
<?php include '../buildit-template.php'; ?>
<style>
article {
   max-width: 50em;
}
</style>
<script>document.getElementById("link-tutorial").classList.add("active");</script>

<h1>High-Performance DSLs targeting GPUs, FPGAs and Accelerators with the BuildIt Framework</h1>
<ul>
<!--<li>CGO 2024, Workshop and Tutorials, <b>8:30 am, 2nd March 2024</b>, Edinburgh, UK</li>-->
<!--<li>DSL Development Workshop@MIT, <b>6th May 2024 3pm - 6pm</b> 32-G882 Stata Center, 32 Vassar St</li>-->
<!--<li>PLDI 2024, Workshop and Tutorials, <b>24th June 2024</b> Copenhagen, Denmark</li>-->
<li>ISCA 2025, Workshop and Tutorials, <b>22nd June 2025</b> Tokyo, Japan</li>







</ul>
<br>
Tutorial Repo with all the code and instructions at - <a href="https://github.com/BuildIt-lang/buildit-array" target="_blank">https://github.com/BuildIt-lang/buildit-array</a>
<br>
<br>

We are organizing a hands-on tutorial on creating high-performance DSLs (Domain Specific Languages) with the BuildIt framework. The BuildIt project makes it possible to rapidly prototype embedded DSLs using a multi-stage programming approach while targeting parallel CPUs, GPUs and now FPGAs. BuildIt is targeted towards domain experts who have limited experience with compiler technology. However it also greatly simplifies the development process for compiler experts allowing implementing analyses, transformations and code generations for various architectures with a fraction of lines of code as compared to traditional compilers.
<br>
This iteration of the tutorial will focus on techniques we have developed to target DSLs for novel architectures like GPUs, FPGAs and accelerators from the same high-level representation. We will use a case study of three different DSLs developed in the past year namely - MARCH, StreamIt and G2 targeting FPGAs, accelerators and GPUs respectively. The tutorial will be completely hands-on where the presenters will cover the basics of BuildIt and code generation followed by live-coding to implement new optimizations to extend these frameworks. The tutorial will cover how to analyze, transform and generate code for these architectures without understanding any compiler related terminology or techniques. As upcoming accelerators and GPUs are becoming ever more useful in the architecture community both in industry and academia we believe this tutorial would be very relevant to early and late-stage PhD students and industry researchers looking to build software stacks for the architectures they develop.
The tutorial will be completely hands-on, with presentations from the organizers and relevant skeleton code shared for development. Attendees are expected to have access to a computer with Linux or MacOS. Windows with WSL also works great. Basic experience with C++ expected. 
<br>
Here is a brief agenda/summary of the topics we plan to cover:
<br>
<ol>
<li> Basics of BuildIt and Recap [1 hr, including setup time]
<ol>
<li>Writing programs in multiple stages using BuildIt’s dyn&lt;T&gt; and static&lt;T&gt; types</li>
<li>Implementing a simple DSL that generates naive code for user programs</li>
</ol>
</li>
<li>Optimizations with BuildIt [3 hr]
<ol>
<li>The ongoing project on NetBlocks and its FPGA implementation supported by JST ASPIRE</li>
<li>Overview and deepdive of the G2 DSL for GPUs</li>
<li>Overview and deepdive of the MARCH DSL for FPGAs</li>
</ol>
</li>
<!--c. Overview and deepdive of the StreamIt DSL targeting accelerators-->
<li>Adding optimizations to the simple DSL
<ol>
<li>Add new optimizations to the toy DSL from different architectures</li>
</ol>
</li>
</ol>
We have free BuildIt swag for all attendees!
<br>
<br>
<b>A part of this tutorial will be supported by JST ASPIRE, Grant Number JPMJAP2430. </b>
<hr>
<h3>Speakers</h3>
<u>Ajay Brahmakshatriya: PhD Student, Massachusetts Institute of Technology</u>
<br>
Ajay is a 7th year PhD student advised by Prof. Saman Amarasinghe at CSAIL, MIT. His research is focused on enabling non-compiler experts to create their own programming languages with focus on high-performance systems domains.
<br><br>

<u>Prof. Yukinor Sato: Professor, Toyohashi University of Technology</u>
<br>Yukinori Sato is an Associate Professor at Toyohashi University of Technology in Japan. He leads the Computer Systems and Performance Engineering Lab, which focuses on architecture of HPC and cloud computing environments and their compiler code optimization technique. He is a PI of an international project “Advanced automatic code optimization via DSL and its compiler for AI infrastructure on edge-cloud computing continuum” supported by JST ASPIRE.

<br><br>
<u>Prof. Saman Amarasinghe, Professor, Massachusetts Institute of Technology</u>
<br>Saman Amarasinghe is the Thomas and Gerd Perkins Professor at EECS and Principal Investigator, CSAIL, MIT. He leads the Commit compiler research group in CSAIL, which focuses on programming languages and compilers that maximize application performance on modern computing platforms. He is a world leader in the field of high-performance domain-specific languages.

<style>
</style>
<?php include '../../../footer.php'; ?>

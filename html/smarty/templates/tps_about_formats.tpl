{extends file="tps.tpl"}
{block name=content}
      
	<h1>Problem File Formats</h1> 
	
 
	<h2>Matrix Market</h2> 
	        <p> 
		Matrix Market (MM) format is an ASCII format designed to represent sparese matricies in an easily parsable format. Most sparse systems on the test problem server are represented using this format and PETSc formated systems can easily be converted on download. For more information about parsing the format, <a href="http://math.nist.gov/MatrixMarket/formats.html#MMformat">visit</a> the Matrix Market website. 
		        </p> 
 
 
         <h2>Elemental (Unassembled) Matrix Market</h2> 
	 <p> Elemental Matrix Market (eMM)is a format developed for use on TxTPS for storing unassembled matrices. It borrows heavily from both the original Matrix Market format and from <a href="http://graal.ens-lyon.fr/MUMPS/doc/userguide_4.8.3.pdf">Mumps</a>.</p> 
	 
	 <p>The format is as follows:</p> 
 
 <p> 
 <ol> 
 <li> A line containing:
 <ul> 
 <li> The global number of variables
        <li> The number of elements
	<li> The number of unassembled variables, that is the sum over all elements of the number of variables per element
        <li> The total number of all numerical values specified, that is, the sum over all elements of the square of the number of variables per element 
   </ul> 
   <li> Lines, one for each element, giving the number of variables for the elements
   <li>Lines, one for each node and each element, ordered first by element, then by local node, giving the global number of the node. 
   
   <ul><li>The global number
   <li>The element number
   <li>The local number in that element 
   </ul> 
													   <li> Lines, one for each value in each element matrix. This is ordered first by element, then columnwise over the values in the element matrix. Again, we can build in redundancy by giving on each line
       <ul> 
													   <li>The value
															              <li>i and j in the element matrix 
																      	    	     	 	 	         <li>i and j in the global numbering
																						       	     	    	             <li>The element number
																										     	     	                   </ul> 
																														   
 
</ol> 
        </p> 
 
 
 
 
                <h2>PETSc</h2> 
		        <p> 
			PETSc binary file format is the native data exchange format for PETSc applications and most PETSc based applications on the Test Problem Server utilize this format saving data. For information on how to load this data in PETSc, <a href="http://www.mcs.anl.gov/petsc/petsc-as/snapshots/petsc-current/docs/manualpages/Sys/PetscBinaryOpen.html">see</a> the PETSc API. This file format can also be converted to Matrix Market on download.  
			        </p> 
				
				        <h2>Converters</h2> 
					<p>One of the key features of the Test Problem Server is ability to easily convert file formats on download. When downloading, select the desired output file format using the drop down menu and the appropriate file formats will be converted.</p> 
 
 <p>The following converters are currently avaliable on the Test Problem Server</p> 
 
 <ul> 
 <li>Matrix Market (MM) -> PETSc</li> 
 <li>PETSc -> Matrix Market(MM) </li> 
 </ul>
{/block}
{block name=sidebar}
<h1>Related Links</h1>     
{/block}

{block name=hidden append}
  <section id="generator_block_template" class="generator_block">
    <a href="#" class="generator_link">
    <div class="generator_image_container">
    <h2 class="generator_name"></h2>
    </div></a>
  </section>
{/block}





{extends file="tps.tpl"}
{block name=head append}
{literal}
<style>
#sidebar{
  display:none;
}
#content{
  height:300px;
}
</style>
{/literal}
{/block}
{block name=content}
<h1>404 Not Found</h1>
<h2>The server could not find the page you were looking for.</h2>
<p>Unfortunately, this happens from time to time. </p>

<p>If you typed in the URL, please check to make sure it is entered correctly.</p>
<p>&nbsp;</p>
<p></p>
{/block}
{block name=sidebar}
<section>
<h1>Common Pages</h1>
</section>
{/block}
{block name=hidden append}
      <section id ="problem_block_template" class="problem_block">
	<div id="problem_image_container">
	     <h3 class="problem_identifier">#abc123</h3>
	     <h4 class="problem_collection">Collection</h4>
	     <h5 class="problem_generator">Generator</h5>
	</div>
       </section>
{/block}
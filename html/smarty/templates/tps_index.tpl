{extends file="tps.tpl"}
{block name=head append}
{literal}
<style>
.problem_identifier{
  color:#fff !important;
}
.problem_generator{
  color:#fff !important;
}
.problem_collection{
  color:#fff !important;
}
</style>
<script>
  var problems;
  var collections;
  var generators;

  function load_data(){
   var collection_uri = "/api/collections/";
    $.ajax({url:collection_uri, async: false, success: function(data){
      collections = JSON.parse(data);
    }});
    while(collections == null){}

   var generator_uri = "/api/generators/";
    $.ajax({url:generator_uri, async: false, success: function(data){
      generators = JSON.parse(data);
    }});
    while(generators == null){}

    var problem_uri = "/api/problems/";
    $.ajax({url:problem_uri, async: false, success: function(data){
      problems = JSON.parse(data);
    }});
    while(problems == null){}
  }

  function display(){
    $.each(problems, function(key,value){      
      var problem = value;
      //create a blank block
      var block = $("#problem_block_template").clone();
      
      var collection = null;
      var generator  = null;
      var i;

      //find generator
      for(i in generators){
        if(generators[i].id == problem.generator_id){
	  generator = generators[i];
	  break;
	}
      }
      
      //find collection
      for(i in collections){
        if(collections[i].id == generator.collection_id){
	  collection = collections[i];
	  break;
	}
      }
      
      
      //fill in values
      block.find(".problem_identifier").text(problem.identifier);
      block.find(".problem_collection").text(collection.name);
      block.find(".problem_generator").text(generator.name);
      //insert into DOM
      block.appendTo($("#recentproblems"));
    });
  }

$(document).ready(function(){
	load_data();
	display();
});
</script>
{/literal}
{/block}

{block name=content_container prepend}
<div id="banner">
  <span style="font-size:20pt" title="Texas Test Problem Server">Texas Test Problem Server </span> <span style="font-size:15pt">is a resource for creating and sharing test problems for numerical linear algebra algorithms.</h1>
</div>
{/block}

{block name=content}
    <section>
    <h2>{$featured_story.title}</h2>
    <p>{$featured_story.body}</p>
    </section>

    <section id="recentproblems" style="height:400px">
      <h2>Recent Problems</h2>
    </section>
{/block}
{block name=sidebar}

      <section id="quicklinks">
	<h4>Quick Links</h4>
	<ul>
	  <li><a href="/help/formats">File Formats</a></li>
	  <li><a href="/collections/">Collections</a></li>
	  <li><a href="/generators/">Generators</a></li>
	  <li><a href="/problems/">Problems</a></li>
	</ul>
      </section>
      <section id="quickstats">
	<h4>TxTPS Stats</h4>
	<table >
	  <tr>
	    <th >Item</th>
	    <th>Count</th>
	  </tr>
	  <tr>
	    <td>Collections</td>
	    <td>23</td>
	  </tr>
	  <tr>
	    <td>Generators</td>
	    <td>150</td>
	  </tr>
	  <tr>
	    <td>Problems</td>
	    <td>3453</td>
	  </tr>
	  <tr>
	    <td>Users</td>
	    <td>30</td>
	  </tr>
	  </table>
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
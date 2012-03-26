{extends file="tps.tpl"}
{block name=head append}
<style>
#sidebar{
  float:left;
  width:150px;
}

#content{
  float:right;
}

.problem_identifier{
  color:#fff !important;
}

.problem_block{
  float:left;
  width:200px;
  height:200px;
  margin: 20px 10px;
}
.problem_image_container{
  padding:10px;
  width:150px;
  height:150px;
  background-color:#555;
}
.problem_link{
  text-decoration:none;
}
</style>
<script>
{literal}
  
  //global lists
  var collections = null;
  var generators  = null;
  var problems    = null;

  function load_data(){

    var problem_uri = "/api/problems/";
    $.ajax({url:problem_uri, async: false, success: function(data){
      problems = JSON.parse(data);
    }});


    var generator_uri = "/api/generators/";
    $.ajax({url:generator_uri, async: false, success: function(data){
      generators = JSON.parse(data);
    }});
    
    var collection_uri = "/api/collections/";
    $.ajax({url:collection_uri, async: false, success: function(data){
      collections = JSON.parse(data);
    }});
  
    while(collections == null || generators == null || problems == null){}
  }

  function display(){
  
    var filtered_problems = filter(problems);
    $("#problems").find(".problems_block").remove();
    $.each(filtered_problems, function(key,value){      
      var problem = value;
      //create a blank block
      var block = $("#problem_block_template").clone();
      //fill in values
      block.find(".problem_identifier").text(problem.identifier);
      //make the block a link
      block.find(".problem_link").attr("href","/problems/" + problem.identifier);
      //insert into DOM
      block.appendTo($("#problems"));

    });
  }

  function filter(){
    var filtered = new Array();
    $.each(problems,function(key,problem){
    
      //don't filter
      filtered.push(problem);
      return;      


      //check collection
      var collection_ids = $("#generator_filters").find("[name=collection_id]:checked").map(function(){ return $(this).val(); }).get();
      if($.inArray(generator.collection_id,collection_ids) < 0){
        return;
      }
      
      //check keywords
      var string = $("#generator_filters").find("[name=global_keyword]").val().toLowerCase();
      if(generator.description.toLowerCase().indexOf(string) < 0 &&
         generator.name.toLowerCase().indexOf(string) < 0){
	 return;
      }
      string = $("#generator_filters").find("[name=description_keyword]").val().toLowerCase();
      if(generator.description.toLowerCase().indexOf(string) < 0){
	 return;
      }

      string = $("#generator_filters").find("[name=name_keyword]").val().toLowerCase();
      if(generator.name.toLowerCase().indexOf(string) < 0){
	 return;
      }

      
      //passed all checks so add to filtered
	filtered.push(generator);
    });
    return filtered;
  }

  function load_filters(){
    while(collections == null){}
   
    //collection filters
    for(var key in collections){
      var collection = collections[key];
      var c_label = $("<label>");
      var c_input = $("<input>");
      var c_p = $("<p>");
      c_label.text(collection.name);
      c_input.attr("type","checkbox");
      c_input.attr("name","collection_id");
      c_input.attr("value",collection.id);
      c_input.attr("checked","true");
      c_input.click(display);
      c_p.append(c_input);
      c_p.append(c_label);
      $("#collection_filter").append(c_p);
      
  }
        
    //keyword filter
    var keyword_filter = $("#generator_filters").find("[name$=keyword]");
    keyword_filter.keyup(function(){
      display();
    });

    //prevent the form from being submitted
    $("#generator_filters").submit(function(){
      return false;
    });
  }

$(document).ready(function(){  
  load_data();
  load_filters();
  display();
});
{/literal}
</script>
{/block}
{block name=content}
    <div id="problems">
      <h1>Problems</h1>
    </div>
{/block}
{block name=sidebar}

      <h1>Filters</h1>
      <form id="generator_filters">
      <section id="collection_filter">
        <h2>Collection</h2>	
      </section>

      <section id="keyword_filter">
        <h2>Keywords</h2>
	<p><label>Name</label><input type="text" name="name_keyword"></p>	
	<p><label>Description</label><input type="text" name="description_keyword"></p>	
	<p><label>Either</label><input type="text" name="global_keyword"></p>	
      </section>
      </form>
     
{/block}
{block name=hidden append}
  <section id="problem_block_template" class="problem_block">
    <a href="#" class="problem_link">
    <div class="problem_image_container">
    <h2 class="problem_identifier"></h2>
    </div></a>
  </section>
{/block}


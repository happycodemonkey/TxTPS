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

.generator_name{
  color:#fff !important;
}

.generator_block{
  float:left;
  width:200px;
  height:200px;
  margin: 20px 10px;
}
.generator_image_container{
  padding:10px;
  width:150px;
  height:150px;
  background-color:#555;
}
.generator_link{
  text-decoration:none;
}
</style>
<script>
{literal}
  
  //global lists
  var collections = null;
  var generators  = null;

  function load_data(){
    var generator_uri = "/api/generators/";
    $.ajax({url:generator_uri, async: false, success: function(data){
      generators = JSON.parse(data);
    }});
    
    var collection_uri = "/api/collections/";
    $.ajax({url:collection_uri, async: false, success: function(data){
      collections = JSON.parse(data);
    }});
  
    while(collections == null || generators == null){}
  }

  function display(){
  
    var filtered_generators = filter(generators);
    $("#generators").find(".generator_block").remove();
    $.each(filtered_generators, function(key,value){      
      var generator = value;
      //create a blank block
      var block = $("#generator_block_template").clone();
      //fill in values
      block.find(".generator_name").text(generator.name);
      //make the block a link
      block.find(".generator_link").attr("href","/generators/" + generator.id);
      //insert into DOM
      block.appendTo($("#generators"));
    });
  }

  function filter(){
    var filtered = new Array();
    $.each(generators,function(key,generator){
      
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
    <div id="generators">
      <h1>Generators</h1>
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
  <section id="generator_block_template" class="generator_block">
    <a href="#" class="generator_link">
    <div class="generator_image_container">
    <h2 class="generator_name"></h2>
    </div></a>
  </section>
{/block}


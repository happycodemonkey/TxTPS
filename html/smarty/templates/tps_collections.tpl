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

.collection_name{
  color:#fff !important;
}

.collection_block{
  float:left;
  width:200px;
  height:200px;
  margin: 20px 10px;
}
.collection_image_container{
  padding:10px;
  width:150px;
  height:150px;
  background-color:#555;
}
.collection_link{
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
    $.each(collections, function(key,value){      
      var collection = value;
      //create a blank block
      var block = $("#collection_block_template").clone();
      //fill in values
      block.find(".collection_name").text(collection.name);
      block.find(".collection_link").attr('href',"/collections/" + collection.id);
      //insert into DOM
      block.appendTo($("#collections"));
    });
  }

$(document).ready(function(){
	load_data();
	display();
});
{/literal}
</script>
{/block}
{block name=content}
    <div id="collections">
      <h1>Collections</h1>
    </div>
{/block}
{block name=sidebar}

      <h1>Filters</h1>
      <section id="quicklinks">
        <h2>Type</h2>
      </section>
      <section id="quickstats">
	<h2># Generators</h2>
      </section>
{/block}
{block name=hidden append}
  <section id="collection_block_template" class="collection_block">
    <a class="collection_link" href="#">
      <div class="collection_image_container">
      <h2 class="collection_name"></h2>
      </div>
    </a>
  </section>
{/block}


{extends file="tps.tpl"}
{block name=head append}
<style>


#related_links a{ 
  display:block;
  clear:both;
  margin-bottom:0.25em;
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
  //global vars
  var collection_id = {$collection_id};
  var collection    = null;
  var generators    =  null;

</script>
<script>
{literal}

  function load_data(){
    
    var collection_uri = "/api/collections/" + collection_id ;
    $.ajax({url:collection_uri, async: false, success: function(data){
      collection = JSON.parse(data);
    }});

    while(collection == null){} //block until collection is loaded

    var generator_uri = "/api/generators/?collection_id=" + collection_id;
    $.ajax({url:generator_uri, async: false, success: function(data){
      generators = JSON.parse(data);
    }});
    
    while(generators == null){} //block until generators are loaded  

  }

  function display(){

    //set title
    var title_text = '';
    title_text    += collection.name;
    $("#content").find("h1").html(title_text);

    //description
    $("#collection_description").append(collection.description);

    //images
    $("#collection_images").append("This collection has no images.");

    //generators
    if(generators.length < 1){
      $("#collection_generators").append("This collection has no generators.");    
    }else{
      $.each(generators, function(key,value){      
        var generator = value;
        //create a blank block
        var block = $("#generator_block_template").clone();
        //fill in values
        block.find(".generator_name").text(generator.name);
        //make the block a link
        block.find(".generator_link").attr("href","/generators/" + generator.id);
        //insert into DOM
        block.appendTo($("#collection_generators"));
      });
    }
    
    
  }




$(document).ready(function(){
  load_data();
  display();
});
{/literal}
</script>
{/block}
{block name=content}
      <h1>Collection  / Generator Name</h1>
      
      <section id="collection_description">
        <h2>Description</h2>
      </section>

      <section id="collection_images">
        <h2>Images</h2>
      </section>

      <section id="collection_generators">
        <h2>Generators</h2>
      </section>


{/block}
{block name=sidebar}


 <section id="related_links">
 <h2>Related Links</h2>
 <a href="/generators/">View all Generators</a>
 <a href="/collections/">View all Collections</a>
 </section>

     
{/block}

{block name=hidden append}
  <section id="generator_block_template" class="generator_block">
    <a href="#" class="generator_link">
    <div class="generator_image_container">
    <h2 class="generator_name"></h2>
    </div></a>
  </section>
{/block}





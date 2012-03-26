{extends file="tps.tpl"}
{block name=head append}
<style>


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

#related_links a{ 
  display:block;
  clear:both;
  margin-bottom:0.25em;
}
</style>
<script>
  //global vars
  var generator_id = {$generator_id};
  var generator  = null;
  var collection = null;
  var args  = null;
</script>
<script>
{literal}



  function load_data(){
    var generator_uri = "/api/generators/" + generator_id;
    $.ajax({url:generator_uri, async: false, success: function(data){
      generator = JSON.parse(data);
    }});
    
    while(generator == null){} //block until generator is loaded
    
    var collection_uri = "/api/collections/" + generator.collection_id ;
    $.ajax({url:collection_uri, async: false, success: function(data){
      collection = JSON.parse(data);
    }});  

    while(collection == null){} //block until collection is loaded

    var argument_uri = "/api/arguments/?generator_id=" + generator_id ;
    $.ajax({url:argument_uri, async: false, success: function(data){
      args = JSON.parse(data);
    }});  

    while(args == null){} //block until arguments are loaded

  }

  function display(){

    //set title
    var title_text = '';
    title_text    += ' <a href="/collections/'+collection.id+'">'+collection.name+'</a> / ';
    title_text    += generator.name;
    $("#content").find("h1").html(title_text);

    //set description
    $("#generator_description").append(generator.description);



    //images
    $("#generator_images").append("This generator has no images.");


    //recent problems
    $("#generator_problems").append("This generator has no recent problems.");
    

    //dynamic related links
    $("#related_links").append('<a href="/collections/'+collection.id+'">View this Collection</a>');

    

  }



$(document).ready(function(){
  load_data();
  display();
});


{/literal}
</script>
{/block}
{block name=content}
      <h1><span class="collection_name">Collection</span> / <span class="generator_name">Generator</span></h1>
      
      <section id="generator_description">
        <h2>Description</h2>
      </section>

      <section id="generator_images">
        <h2>Images</h2>
      </section>

      <section id="generator_problems">
        <h2>Recent Problems</h2>
      </section>


{/block}
{block name=sidebar}

 <section>
 <h2>Build</h2>
 <p>To create a test problem using this generator, click the button below:</p>
 <form >
 <input type="button" onclick="tps_build(generator.id,{})" value="Create a Problem" ">
 </form>
 </section>

 <section id="related_links">
 <h2>Related Links</h2>
 <a href="/generators/">View all Generators</a>
 <a href="/collections/">View all Collections</a>
 </section>

     
{/block}



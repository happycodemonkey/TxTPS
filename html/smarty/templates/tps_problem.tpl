{extends file="tps.tpl"}
{block name=head append}
<style>


#file_statistics{
  width:400px;
  height:500px;
  color:white;
}

#file_statistics td{
  font-size:12px;
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

#related_links a{ 
  display:block;
  clear:both;
  margin-bottom:0.25em;
}


</style>
<script>
  //global vars
  var problem_identifier = "{$problem_id}";
  var problem    = null;
  var generator  = null;
  var collection = null;
  var args       = null;
  var files      = null;
  var images     = null;

</script>
<script>
{literal}

  function load_data(){

    var problem_uri = "/api/problems/?identifier=" + problem_identifier;
    $.ajax({url:problem_uri, async: false, success: function(data){
      problem = JSON.parse(data)[0];
    }});

    while(problem == null){} //block until problem is loaded

    var generator_uri = "/api/generators/" + problem.generator_id;
    $.ajax({url:generator_uri, async: false, success: function(data){
      generator = JSON.parse(data);
    }});
    
    while(generator == null){} //block until generator is loaded
    
    var collection_uri = "/api/collections/" + generator.collection_id ;
    $.ajax({url:collection_uri, async: false, success: function(data){
      collection = JSON.parse(data);
    }});
  
    while(collection == null){} //block until collection is loaded

    //arguments
    var argument_uri = "/api/arguments/?generator_id=" + generator.id;
    $.ajax({url:argument_uri, async: false, success: function(data){
      args = JSON.parse(data);
    }});
  
    while(args == null){} //block until args are loaded

    //files
    var file_uri = "/api/files/?resource_id=" + problem.id;
    $.ajax({url:file_uri, async: false, success: function(data){
      files = JSON.parse(data);
    }});

    //images
    var image_uri = "/api/images/?resource_id=" + problem.id;
    $.ajax({url:image_uri, async: false, success: function(data){
      images = JSON.parse(data);
    }});

  
    while(images == null){} //block until images are loaded



  }

  function display(){

    //set title
    var title_text = '';
    title_text    += ' <a href="/collections/'+collection.id+'">'+collection.name+'</a> / ';
    title_text    += ' <a href="/generators/'+generator.id+'">'+generator.name+'</a> / ';
    title_text    += problem.identifier;
    $("#content").find("h1").html(title_text);

    //set description
    $("#generator_description").append(generator.description);


    //images
    if(images.length > 0){
      var image_gallery = $("<div id='gallery'>");
      $("#problem_images").append(image_gallery);
      for (key in images){
        value = images[key];
	var path = value.name;
	var full_path = "/problems/" + problem_identifier + "/images/" + path;
        image_gallery.append("<a href='"+full_path+"'><img src='"+full_path+"' width='100' height='100'></a>");
      }
    }else{
    $("#problem_images").append("<p>No images were found.</p>");
    }



    //args
    var args = JSON.parse(problem.arguments);
    var key;
    var value;
    var arg_table = $("<table><tr><th>Name</th><th>Value</th></tr>");
    $("#problem_arguments").append(arg_table)
    for (key in args){
      value = args[key];
      arg_table.append("<tr><td>" + key + "</td><td>" + value + "</td></tr>");      
    }

    
    //files
    if(files.length > 0){
      var file_table = $("<table><tr><th>#</th><th>Name</th><th>Statistics</th></tr>");
      $("#problem_files").append(file_table);
      for (key in files){
        value = files[key];
	var path = value.name;
	var full_path = "/problems/" + problem_identifier + "/files/" + path;
	var file_row  = $("<tr>");
        file_table.append(file_row);
	file_row.append("<td>"+(parseInt(key) + 1)+"</td>");
        file_row.append("<td><a href='"+full_path+"'>"+path+"</a></td>");    
        if(value.statistics != null){
	  file_row.append("<td><input type='button' onclick='file_statistics("+value['id']+")' value='Statistics'/></td>");    
	}else{
	  file_row.append("<td><input type='button' disabled='true' value='Statistics'/></td>");    
	}
//        file_row.append("<td><a href='"+full_path+"'>"+path+"</a></td>");    
//        file_row.append("<tr><td>"+(parseInt(key) + 1)+"</td><td><a href='"+full_path+"'>"+path+"</a></tr>");    
      }
    }else{
    $("#problem_files").append("<p>No files were found.</p>");
    }


    //download link
    $('#download_identifier').val(problem_identifier);
    

    //dynamic related links
    $("#related_links").append('<a href="/collections/'+collection.id+'">View this Collection</a>');
    $("#related_links").append('<a href="/generators/'+generator.id+'">View this Generator</a>');
  }


function modify_problem(){
  var default_values = JSON.parse(problem.arguments);
  var note_text = "This problem is a modification of problem " + problem.identifier + ".";
  tps_build(generator.id,default_values, note_text);
}


function file_statistics(file_id){
  var container = $("#file_statistics");
  var f = null;
  var i = 0;
  var j = 0;

  //clear
  container.empty();
 
 
  for(i in files){
    if(files[i].id == file_id){
      f = files[i];
      break;
    }
  }

  //not found
  if(f == null){
    return;
  }

  //header
  container.append("<h1>"+f.name+"</h1>");

  //group by prefix
  var properties = f.statistics;
  var buckets = new Object();
  for(i in properties){
    var key      = i;
    var value    = properties[key];
    var property =  new Object();
    property[key] = value;
    var parts    = key.split("-");
    var prefix   = parts[0];

    //create bucket if needed
    if(!(prefix in buckets)){
      buckets[prefix] = new Array();
    }

    //add to bucket
    buckets[prefix].push(property);
  }
  

  //remove file_id property group
  delete buckets["file_id"];

  //create blocks
  for(i in buckets){
    var bucket_name = i;
    var bucket      = buckets[i];
    var bucket_div  = $("<div class='property_group'>");
    bucket_div.hide();
    bucket_div.appendTo(container);
    var bucket_hdr  = $("<h2>" +bucket_name+"</h2>");
    bucket_hdr.appendTo(bucket_div);

    //properties inside this bucket
    var property_table = $("<table>");
    property_table.appendTo(bucket_div);
    property_table.append("<tr><th>Key</th><th>Value</th></tr>");
    for(j in bucket){
      var property = bucket[j];
      var key      = "Key";//property.keys()[0];
      var value    = "Value";property[key];
      console.log(bucket[j][0]);
      property_table.append("<tr><td>"+key+"</td><td>"+value+"</td></tr>");
    }
    
  }

  //make the first one visible
  container.children(".property_group").first().show();
  
  //navigation
  var navigation_div = $("<div>");
  navigation_div.css("position","absolute");
  navigation_div.css("bottom","10px");
  
  navigation_div.appendTo(container);
  var next_button    = $("<input type='button' value='Next Property Group'>");
  next_button.appendTo(container);
  next_button.css("position","absolute")
  next_button.css("bottom","10px");
  next_button.css("right","10px");
  var prev_button    = $("<input type='button' value='Prev Property Group'>");
  prev_button.appendTo(container);
  prev_button.css("position","absolute")
  prev_button.css("bottom","10px");
  prev_button.css("left","10px");


  //animate
  next_button.click(function(){
    var curr  = container.children(".property_group:visible");
    var next  = curr.next(".property_group");
    curr.hide();
    next.show();
  });

  prev_button.click(function(){
    var curr  = container.children(".property_group:visible");
    var prev  = curr.prev(".property_group");
    curr.hide();
    prev.show();
  });


  //display
  container.modal({escClose : true});
}

$(document).ready(function(){
  load_data();
  display();
  $('#gallery a').lightBox(); // Select all links in object with gallery ID
});
{/literal}
</script>
{/block}
{block name=content}
      <h1>Collection  / Generator Name</h1>
      
      <section id="generator_description">
        <h2>Description</h2>
      </section>

      <section id="problem_images">
        <h2>Images</h2>
      </section>

      <section id="problem_arguments">
        <h2>Arguments</h2>
      </section>

      <section id="problem_files">
        <h2>Files</h2>
      </section>


{/block}
{block name=sidebar}

 <section>
 <h2>Modify Problem</h2>
 <p>To create a test problem similar to this one, click the button below:</p></br>
 <form>
 <input type="button" value="Modify this Problem" onclick="modify_problem()">
 </form>
 </section>

 <section>
 <h2>Download</h2>
 <p>To download the entire problem including data files, images, etc. in an archive, click the button below:<p></br>
 <form action="/download.php">
 <input type="hidden" name="mode" value="NONE">
 <input type="hidden" name="identifier" value="" id="download_identifier">
 <input type="submit" value="Download this Problem">
 </form>
 </section>


 <section id="related_links">
 <h2>Related Links</h2>
 <a href="/generators/">View all Generators</a>
 <a href="/collections/">View all Collections</a>
 </section>

     
{/block}

{block name=hidden append}
<div id="file_statistics">

</div>
{/block}

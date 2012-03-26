<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php 
include_once("../includes/db/generators.inc.php");
$collection_list = generator_collection_list();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script src="http://www.google.com/jsapi?key=ABQIAAAAFm_alaZoE-oHFncIiaLcIxSvXgVckI6g_NvjnVRrpDD27K_tUxThinip_QAXOlx_WYg32QPVCEBh5A" type="text/javascript"></script>
<script type="text/javascript">
  var collections;
  var generators;


  function start(){
    collections_url = "/tacc/api.php?path=/collections/";
    generators_url  = "/tacc/api.php?path=/generators/";
    $.ajax({async:false,url:collections_url,success:function(json){
      collections = JSON.parse(json);
    }});
    $.ajax({async:false,url:generators_url,success:function(json){
      generators = JSON.parse(json);
    }});


   load_list();
    (function(){ 
      old = "";
      setInterval(function(){
		    if(old !=window.location.hash){ //changed
		      hash_handler(window.location.hash);
		      old = window.location.hash;
		    
		    }
		  },100);
    }()); //end of foo

  }


function set_step(step){
  
  $("#step1label").removeClass("selected");
  $("#step2label").removeClass("selected");
  $("#step3label").removeClass("selected");
  $("#step4label").removeClass("selected");
  $(".step").hide(); 
  $(".step_button").find(".check").hide();

  for(i = 1; i < step; i++){
    $("#step" + i + "check").show(); 
  }

  $("#step" + step).show();
  $("#step"+ step + "label").addClass("selected");  

}

function validate(input){

  type     = "STRING";
  required = false;
  value    = input.val();
  valid    = false;
  
  
  //get type value
  if(input.attr("vtype") != undefined){
    type = input.attr("vtype").toUpperCase();
  }

  //get required value
  if(input.attr("required") != undefined){
    required = input.attr("required");
  }

  //handle required 
  if(required){
    if(value.length < 1){
      input.effect("highlight");
    }
  }

  
  if(type == "STRING"){
    valid = true;
  }
  else if(type == "INTEGER"){
    
    x = parseInt(value);
  
    if(isNaN(x)){
      valid = false;
    }else{
      valid = true;
      value = x + "";
    }
  }
  else if(type == "FLOAT" || type == "DOUBLE"){
  
    x = parseFloat(value);
  
    if(isNaN(x)){
      valid = false;
    }else{
      valid = true;
      value = x + "";
   
      //try to add a decimal point
      if(value.indexOf(".") < 0){
	value = value + ".0";
      }
    }
  }
  else if(type == "SELECT"){
    valid = true;
  }
  else if(type == "BOOLEAN"){
    valid = true;
  }

  inputTD  = input.parent();
  checkTD  = inputTD.next();
  commTD   = checkTD.next();
  inputTR  = inputTD.parent();

  if(valid){
    //reassign normalized value
    input.val(value);

    checkTD.hide();
    commTD.hide();
  }else{
    input.val("");

    input.effect("highlight","",2000);
    checkTD.show();
    commTD.html("Not a valid " + type.toLowerCase());
    commTD.show();
  }
 
}


function load_list(){
  

	 //clear generator form
	 $("#groupform").empty();
	   
	 for ( i in collections){
	   collection  = collections[i];

	   //various IDs
	   input_id   = "collection_radio_" + collection.id;
	   header_id  = "collection_header_" + collection.id;
	   body_id    = "collection_body_" + collection.id;
	     		
	   col_div    = $("<div>");
	   col_div.appendTo($("#groupform"));
	     
	   //header
	   header = $("<div>");
	   header.addClass("collection_header");
	   header.appendTo(col_div);
	   col_input  = $("<input>");
	   col_input.attr("type","radio");
	   col_input.attr("name","collection_id");
	   col_input.attr("id" , input_id);
	   col_input.val(collection.id);
	   col_input.appendTo(header);
	   col_input.click(function(){
	       
	     collection_id = $(this).val();
	       
	     //hide all the bodies
	     $(".collection_body").hide();
 	     $(".collection_header").removeClass("selected");
             $(".collection_header").find(".check").hide();
	     //show this one
	     $("#collection_body_" + collection_id).show();  
	     //select
	     $(this).parents(".collection_header").addClass("selected");
	     $(this).parents(".collection_header").find(".check").show();

	   });
	   col_label  = $("<label>");
	   col_label.addClass("header_label");
	   col_label.appendTo(header);
	   col_label.text(collection.name);
	   col_label.attr("for", input_id);


	   //check mark
	   check = $("<span>");
	   check.addClass("check");
	   check.html("&#x2713;");
	   check.appendTo(header);


	   //description
	   body_div   = $("<div>");
	   body_div.attr("id","collection_body_" + collection.collection_id);
	   body_div.addClass("collection_body");
	   body_div.appendTo($("#collection_bodies"));

	     
	   clean_text = collection.description;
	   if(($.trim(clean_text)).length < 1){
	     clean_text = "No Description Found."
	   }
	   colname = $("<h2>");
	   col_list_link = $("<a>");
	   col_list_link.attr("href","#");
	   col_list_link.text("Collections");
	   colname.append(col_list_link);
	   colname.append(" > ");
	   colname.append(collection.name);

	   body_div.append(colname);
	   body_div.append($("<h2>Description</h2>"));
	   body_div.append($("<span>" + clean_text + "</span>"));
	 }


	 //hide all the bodies
	 $(".collection_body").hide();
	 //show first one
	 $(".collection_header").first().find('input[type="radio"]').click();

	 //update the click links
	 $("#step1label").unbind();
	 $("#step2label").unbind();
	 $("#step3label").unbind();
	 $("#step4label").unbind();
	  
	 //perform transition   	  
  	 set_step(1);
}


function load_collection(id){


  //find collection
  collection;
  for (i in collections){
    if(collections[i].id == id){
      collection = collections[i];
      break;
    }
  }
  


  //extract generators with this collection ID
  gens = [];
  for (i in generators){
    if(generators[i].collection_id == id){
      gens.push(generators[i]);
    }
  }

  document.title = "TxTPS -  Collection " + id;
	   
  //clear generator form
  $("#generatorform").empty();
	   
  for ( i in gens){
    generator  = gens[i];

    //various IDs
    input_id   = "generator_radio_" + generator.id;
    header_id  = "generator_header_" + generator.id;
    body_id    = "generator_body_" + generator.id;
	     		
    gen_div    = $("<div>");
    gen_div.appendTo($("#generatorform"));
	     
    //header
    header = $("<div>");
    header.addClass("generator_header");
    header.appendTo(gen_div);
    gen_input  = $("<input>");
    gen_input.attr("type","radio");
    gen_input.attr("name","generator_id");
    gen_input.attr("id" , input_id);
    gen_input.val(generator.id);
    gen_input.appendTo(header);


    gen_input.click(function(){

      generator_id = $(this).val();

      //hide all the bodies
      $(".generator_body").hide();
      $(".generator_header").removeClass("selected");
      $(".generator_header").find(".check").hide();
      // show this one
      $("#generator_body_" + generator_id).show();
      $(this).parents(".generator_header").addClass("selected");
      $(this).parents(".generator_header").find(".check").show();

    });
    gen_label  = $("<label>");
    gen_label.addClass("header_label");
    gen_label.appendTo(header);
    gen_label.text(generator.name);
    gen_label.attr("for", input_id);


    //check mark
    check = $("<span>");
    check.addClass("check");
    check.html("&#x2713;");
    check.appendTo(header);


    //description
    body_div   = $("<div>");
    body_div.attr("id","generator_body_" + generator.id);
    body_div.addClass("generator_body");
    body_div.appendTo($("#generator_bodies"));

    genname = $("<span>");
    col_list_link = $("<a>");
    col_list_link.attr("href","#");
    col_list_link.text("Collections");
    genname.append(col_list_link);
    genname.append(" > ");
    col_link = $("<a>");
    col_link.attr("href","#collection," + collection.id);
    col_link.text(collection.name); 
    genname.append(col_link);
    genname.append(" > ");
    genname.append(generator.name);     
    

    clean_text = generator.description;
    if(($.trim(clean_text)).length < 1){
      clean_text = "No Description Found."
    }
    body_div.append(genname);
    body_div.append($("<h2>Description</h2>"));
    body_div.append($(clean_text));
  }


  //show first one
  $(".generator_header").first().find('input[type="radio"]').click();

  //update the click links
  $("#step1label").unbind();
  $("#step2label").unbind();
  $("#step3label").unbind();
  $("#step4label").unbind();
  $("#step1label").click(function() {
    window.location.hash = ""
  });

	  
  //perform transition 
  set_step(2);
}


function load_generator(id){
  
  //load generator information
  url = "/tacc/api.php?path=/generators/"+id;
  $.get(url, function(data){

    //parse json
    generator = jQuery.parseJSON(data);

    //update globals 
    generator_id    = generator.id;
    collection_id   = generator.collection_id;


  });

  
 
  //load arguments
  url = "/tacc/api.php?path=/arguments/?generator_id="+id;
  $.get(url, function(data){
	   json_obj      = jQuery.parseJSON(data);
	   document.title = "TxTPS -  Generator " + id
	   collection_id  = "";
	   params         = json_obj;
	   new_html       = "";
	   for ( i in params){
	     
	     pvar      = params[i].variable;
	     pdefval   = params[i].default_value;
	     ptype     = params[i].type;
	     pname     = params[i].name;
	     pid       = "param_" + pvar;
	     poptions  = params[i].options;

	     new_html+= "<tr><td>";
	     new_html += "<label for=\"" + pid + "\">" + pname + "</label>";
	     new_html+= "</td><td>";
	     
	     if(ptype == "STRING"){
	       new_html+= "<input onChange=\"validate($(this));\" vtype=\""+ptype+"\" id=\""+pid+"\" type=\"text\" name=\"" + pvar + "\" value=\"" + pdefval + "\"/>"; 
	     } else if(ptype == "INTEGER"){
	       new_html+= "<input onChange=\"validate($(this));\" vtype=\""+ptype+"\" id=\""+pid+"\" type=\"text\" name=\"" + pvar + "\" value=\"" + pdefval + "\"/>";
	     } else if(ptype == "BOOLEAN"){
	       new_html+= "<input onChange=\"validate($(this));\" vtype=\""+ptype+"\" id=\""+pid+"\" type=\"checkbox\" name=\"" + pvar + "\" value=\"" + pdefval + "\"/>";
	     } else if(ptype == "FLOAT" || ptype == "DOUBLE"){
	       new_html+= "<input onChange=\"validate($(this));\" vtype=\""+ptype+"\" id=\""+pid+"\" type=\"text\" name=\"" + pvar + "\" value=\"" + pdefval + "\"/>";
	     } else if(ptype == "SELECT"){
	       new_html+= "<select onChange=\"validate($(this));\" vtype=\""+ptype+"\" id=\""+pid+"\" type=\"text\" name=\"" + pvar + "\">";
	       
	       for( j in poptions ){
		 
		 oname = poptions[j];
		 oval  = poptions[j];
		 if(oval == pdefval){
		   new_html+= "<option selected value=\"" + oval + "\">" + oname + "</option>"; 
		 }else{
		   new_html+= "<option value=\"" + oval + "\">" + oname + "</option>"; 
		 }
	       }
		 
	       new_html+="</select>";
	     }
	     
	     new_html+= "</td><td style=\"display:none\">&#x2717;</td><td style=\"display:none\">Comment</td></tr>";
	   }
	   
	   //update the HTML
	   $("#parameter_list").html(new_html);
	   
	   
	   //update the click links
	   $("#step1label").unbind();
	   $("#step2label").unbind();
	   $("#step3label").unbind();
	   $("#step4label").unbind();
	   $("#step1label").click(function() {
				    window.location.hash = "";
				  });
	   $("#step2label").click(function() {
				    window.location.hash = "collection," + collection_id;
				  });

	   //perform the transition
	   set_step(3);

	   generator_url = "/tacc/api.php?path=/generators/" + id;
	   $.get(generator_url, function(generator_json){
	     
	     gen = JSON.parse(generator_json);
	     collection_id = gen.collection_id;
	     
	     //update navigation
	     collection_url = "/tacc/api.php?path=/collections/?collection_id=" + collection_id;
	     $.get(collection_url, function(collection_json){
	     
	       cols = JSON.parse(collection_json)
	       col  = cols[0];
	     
	       //collection list link
	       navbar = $("#navigation");
	       navbar.empty();
	       collections_link = $("<a>");
	       collections_link.attr("href","#");
	       collections_link.text("Collections");
	       collections_link.appendTo(navbar);

	       //append collection
	       navbar.append(" > ");
	       collection_link = $("<a>");
	       collection_link.attr("href","#collection," + col.id);
	       collection_link.text(col.name);
	       collection_link.appendTo(navbar);

	       //append collection
	       navbar.append(" > ");
	       generator_link = $("<span>");
	       generator_link.text(gen.name);
	       generator_link.appendTo(navbar);

	       document.title = "TxTPS - " + gen.name;


   	     });

	   });

	 });
  

}


function load_status(product_id){

	   //update the click links
 	   $("#step1label").unbind();
	   $("#step2label").unbind();
	   $("#step3label").unbind();
	   $("#step4label").unbind();
	
	   //perform the transition
	   set_step(4);
}



function hash_handler(hash){
    
  //empty hash -> list out groups
  if(hash.length < 2){
    load_list();
  }
  
  //remove # character
  hash = hash.substr(1);
  
  //parse
  parts = hash.split(",");
  htype = parts[0];
  hval  = parts[1];

  

  if(htype == "collection"){   //collection type => load generators
    load_collection(hval);
  }else if(htype == "generator"){   //generator type => load option
    load_generator(hval);
  }else if(htype == "B"){   //build type = > load status page
    load_status(hval);
  }
}


function build(){

   problem = new Object;
  
   // handle arguments
   fields = $("#parameterform").serializeArray();
   params = new Object;
   jQuery.each(fields, function(i, field){
      params[field.name] = field.value;
   });
   problem["arguments"] = params;

   //build up object
   problem["generator_id"] = generator_id;
     
    
   // serialize 
   json_problem = JSON.stringify(problem);

   //submit     
   jQuery.ajax({contentType: "application/json", data: json_problem, type: "POST", url:"/tacc/api.php?path=/products/", success: function(data){
       
     product    = JSON.parse(data);
     identifier = product["identifier"];
     url = "/matrices/matrix.php?id=" + identifier; 
     window.location = url;
       
   }});
}


  google.load("jquery", "1");
  google.load("jqueryui", "1");
  google.setOnLoadCallback(start); 

</script>
<link rel="stylesheet" type="text/css" href="/css/main.css">
<style type="text/css">
th,td{
	padding:0 1em 0 1em;
}

input[type="radio"]{
# display:none;
}


#wrapper{
min-width:80em;
}

#collection_bodies,#generator_bodies{
  display:block;
  float:left;
}

#collection_names,#generator_names{
  width:30em;
  display:block;
  float:left;
}


#navigation{
 display:none;
 float:left;
 width:10em;
 font-size:1.25em;
}

.header_label{
  font-size:1.5em;
  height:2em;
  margin-left:0.25em;
}

.header_label:hover{
# margin-left:0.25em;
}

.collection_body,.generator_body{
  max-width:50em;
  margin-top:-1em;
}

.next_button{
width:10em;
margin-right:1em;
}

.step{
display:block;
float:left;
}

.step_button{
  font-size:1.5em;
  display:block;
  width:15em;
  height:2em;
  #order:solid 5px;
  margin-bottom:0.5em;
   margin-left:-2em;
   margin-right:-2em;
}

.selected{
   font-weight:600;
}

.state_box{
  border:solid 2px;
  height:10em;
  display:inline-block;
  float:left;
  margin:0.5em;
}

.state_label{
  margin-left:auto;
  margin-right:auto;
  font-size:1.5em;
  font-weight:bold;
   margin-left:-3em;
  border:solid 1px red;
  display:block;
  float:left; 
 padding-top:3em;
  -webkit-transform: rotate(-90deg); 
  -moz-transform: rotate(-90deg);
  filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
}

.state_content{
  float:left;
  display:block;
}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Test Problem Server</title>
</head><body>
<div id="wrapper">
	<div id="titlebar">
		<?php include("../includes/templating/title.inc.php"); ?>
	</div>
	   <div id="login">
              <?php require("../includes/templating/login.inc.php"); ?>
	   </div>

	<div id="menu">
		<?php include("./menu.inc.php"); ?>
	</div>
	<div id="sidemenu">
		<?php include("./submenu.inc.php"); ?>
	</div>
	<div id="content">
    <div style="border-right-style:solid;border-right-width:1px;padding:2em;padding-top:1em;padding-bottom:-0.5em;margin-top:2em;display:block;float:left;margin-right:2em;display:none;"> 

    <span id="step1label" onclick="" class="step_button selected"> Step 1: <span id="step1text">Select Group</span><span id="step1check" class="check" style="display:none">&#x2713;</span></span>
    <span id="step2label" onclick="" class="step_button"> Step 2: <span id="step2text">Select Generator</span> <span id="step2check" class="check" style="display:none">&#x2713;</span></span>
    <span id="step3label" onclick="" class="step_button"> Step 3: <span id="step3text">Parameters</span><span id="step3check" class="check" style="display:none">&#x2713;</span></span>
    <span id="step4label" onclick="" class="step_button"> Step 4: <span id="step4text">Build </span></span>
    
    </div>
  <div class="foldable" style="display:block;float:left">
  
  <div id="navigation"><span class="head">Problem Selections</span></div> 
  <div class="step" id="step1">
  <h1 class="head">Step 1: Select Collection</h1>
  <div>
  <div id="collection_names">
  <form id="groupform">
  </form>
   <input class="next_button" style="visibility:hidden" type="button" value="Previous Step">
   <input class="next_button" type="button" onclick='cid =$("input[name=\"collection_id\"]:checked").val();location.href = "#collection," + cid;' value="Next Step">
  </div>
   <div id="collection_bodies">
  </div>
  </div>
  <br>
  </div>

  <div class="step" id="step2">
  <h1 class="head">Step 2: Select Generator</h1>
  <div id="generator_names">
  <form id="generatorform">
  </form>
  <input class="next_button" type="button" onclick='history.go(-1);' value="Previous Step">
  <input class="next_button" type="button" onclick='gid =$("input[name=\"generator_id\"]:checked").val();location.href = "#generator," + gid;' value="Next Step">  
  </div>
  <div id="generator_bodies">
  </div>
  <br>
  </div>

  <div class="step" id="step3">
  <h1 class="head">Step 3: Parameters</h1>
  <form id="parameterform">
  <table>
  <tbody id="parameter_list">
  </tbody>
  </table>
  </form>
  <input class="next_button" type="button" onclick='$("#step2label").click();' value="Previous Step">
  <input class="next_button" type="button" onclick='build()' value="Next Step">
  </div>
  
  <div class="step" id="step4">
  <h1 class="head" style="width:2;">Step 4: Build</h1>
  
  
  <p>Your request is being built.</p>
    

  </div>
 


 </div>

  <div id="footer">
 </div>
</div>
</body></html>

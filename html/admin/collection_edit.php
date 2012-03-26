<?
include_once("../includes/db/users.inc.php");
include_once("../includes/db/generators.inc.php");
include_once("../includes/db/data.inc.php");
require_once("../includes/classes/Security.class.php");

$security = Security::getInstance();
if(!$security->isLoggedIn() || $security->getClassName() !== "Admin"){
  Security::forward("/login.php");
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" type="text/css" href="/css/admin.css">
<script type="text/javascript" src="/js/jquery.js">
<script type="text/javascript" src="/js/tpsadmin.js">
<script type="text/javascript">
</script>

<script type="text/javascript">

var collection_id = <?php echo $_GET['id']; ?>;
var generator_name;
var collection_name;

var changed = false;
var loaded  = false;

function start(){
  (function(){ 
  old = "";
  setInterval(function(){
	  if(old !=window.location.hash){ //changed
	     hash_handler(window.location.hash);
	     old = window.location.hash;
	   }
	  },100);
  }()); 

  $("#overview_tab_label").click(function(){
    set_tab("overview");
  });

  $("#arguments_tab_label").click(function(){
    set_tab("arguments");
  });

  $("#validation_tab_label").click(function(){
    set_tab("validation");
  });

  $("#processing_tab_label").click(function(){
    set_tab("processing");
  });

  $("#delete_tab_label").click(function(){
    set_tab("delete");
  });

  load(collection_id);
  hash_handler(window.location.hash);

}

  function set_tab(tab){
    //check if changes have occured
    if(changed){
      ignore_changes = confirm("You have made changes without saving, do you wish to continue and lose these?");
      if(!ignore_changes){
        return;
      }
      changed = false;
    }


    window.location.hash= "" + tab;
  }

  function hash_handler(hash){
    var tab;
   
    if(hash.length < 3){ //no valid hash
      tab = "overview";
    }else{ // valid hash
      tab = hash.substr(1);  //remove #
    }

    $("div.tab").hide();
    $("div.#" + tab).show();

    $(".tab_button").removeClass("selected");
    $("#" + tab + "_tab_label").addClass("selected");
  }

  function load(id){
    collection_id  = id;    

    if(loaded){
      return;
    }

    //load generator
    col_url = "/tacc/api.php?path=/collections/" + collection_id;
    $.get(col_url,function(data){
      collection = JSON.parse(data);
      $("#collection_name").val(collection["name"]);    
      $("#collection_description").val(collection["description"]);
      
      //update text
      $(".collection_name").text(collection["name"]);    
    });
    
  }



  function save_overview(){
    form   = $("#collection_form");
    fields = form.serializeArray();
    
    collection = new Object;

    for (i in fields){
      f     = fields[i];
      name  = f.name;
      value = f.value;
      collection[name] = value;
    } 

    col_json = JSON.stringify(collection);
    col_url  = "/tacc/api.php?path=/collections/" + collection_id;
    //alert(gen_json);
    jQuery.ajax({type:"PUT", data:col_json,url:col_url, success:function(data){
       //update name
       $(".collection_name").text($("#collection_name").val());
    }, error: function(){
      alert("Could not Save");
    }});

  }

 function delete_collection(){

   $.ajax({
	url:"/tacc/api.php?path=/collections/" + collection_id,
	type: "delete",
	success: function(){
	  alert("The collection was deleted.");
	  window.location = "collection.php";
	},
	error: function(){
	  alert("An error occured and the collection was not deleted");
	}});
 }


</script>
<link  rel="stylesheet" type="text/css" href="../css/main.css" />
<style type="text/css">
th,td{
	padding:.125em .5em .125em .5em;
}
.panelHeader{
	font-weight:bold;
	font-size:medium;
	cursor:pointer;
}
.mdHover{
	color:#666;
}
.mdSelected{
	color:#00CCFF;
}

.example{
  padding-left:1em;
}

.tab{
  display:block;
  float:left;

}
de
.actions{
 padding-right:3em;

}

.fields{
  padding-left:1em; 
  display:block;
  border-right:1px dashed grey;
  border-left:1px dashed grey;
}

.controls input{
  width:5em;
}

.argument{
  display:block;
  clear:both;
  border-bottom: 1px solid grey;
}

.tab_button{
  font-size:1.5em;
  display:block;
  width:10em;
  height:2em;
  margin-bottom:0.5em;
   margin-left:-2em;
   margin-right:-2em;
}

.selected{
   font-weight:bold;
}

div.fields input[type=text]{
  width: 20em;
}

div.fields select{
  width: 20.5em;
}

div#overview input[type=text]{
  width: 20em;
}

div#overview select{
  width: 20.5em;
}

</style>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Test Problem Server</title>
</head>
<body onload='start()'>
<div id="wrapper">
  <div id="titlebar">
  <?php include("../includes/templating/title.inc.php"); ?>
</div>
<div id="menu">
  <?php include("../includes/templating/adminmenu.inc.php"); ?>
</div>

<div id="content">
<h1><span id="overview_title"><span class="generator_name"></span><a href="/admin/">Admin</a> / <a href="/admin/collections">Collections</a> / <span class="collection_name"></span></span></h1>
  <div style="border-right-style:solid;border-right-width:1px;padding:2em;display:block;float:left;margin-right:2em;">
    <span id="overview_tab_label"   class="tab_button selected"> Overview </span>
    <span id="delete_tab_label" class="tab_button"> Delete </span>
  </div>

<div id="overview" class="tab">
<div class="section">
<h2>Basics</h2>
<form id="collection_form">
<table>
<tr><td><label for="collection_name">Name:</label></td><td><input type="text" name="name" value="" id="collection_name"></td></tr>
<tr><td><label for="collection_description">Description:</label></td><td><textarea cols="80" rows="6" id="collection_description" name="description"></textarea></td></tr>
</table>

<br><br>
<input type="button" value="Save Overview" onclick="save_overview()">
</form>
</div>
</div>


<div id="delete" class="tab">

<div class="section">
<h2>Delete</h2>
<p>If are are sure you want to delete this collection, click the button below. This operation cannot be undone.</p>
<input type="button" value="Delete This Collection" onclick="delete_collection()">
</div>
</div>

</div>

<div id="footer">
</div>
</div>
</body>
 </html>
  
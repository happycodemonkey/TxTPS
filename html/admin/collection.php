<?
require_once("../includes/classes/Security.class.php");
$security = Security::getInstance();
if(!$security->isLoggedIn()){
  Security::forward("../login.php");
 }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" type="text/css" href="../css/main.css">
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript">

var collections;
var generators;

function start(){
  load();
  build_table();
}

function build_table(){
  
  //build headers
  var table = $("#collection_table");
  var columns = ["Name","Number of Generators"];
  var i;
  var tr = $("<tr>");
  for(i in columns){
    var th = $("<th>");
    th.text(columns[i]);
    th.appendTo(tr);
  }
  tr.appendTo(table);
  
  //build rows
  var id;
  for (id in collections){
    insert_collection(collections[id]);  
  }
}

function insert_collection(collection){
  var table    = $("#collection_table");
  var row      = $("<tr>");
  row.appendTo(table);

  var name_td  = $("<td>");
  var clink    = $("<a>");
  clink.attr("href", "/admin/collections/" + collection.id + "/edit");
  clink.text(collection.name);
  name_td.append(clink);
  name_td.appendTo(row);

  var number_of_generators = 0;

  for(i in generators){
    if(generators[i].collection_id == collection.id){
      number_of_generators += 1;
    }
  }

  var num_td  = $("<td>");
  num_td.text(number_of_generators);
  num_td.appendTo(row);
 
}

function load(){
  var collections_url = "/tacc/api.php?path=/collections/";
  var cols;
  $.ajax({url:collections_url, async: false, success: function(data){
    collections = new Object();
    cols = JSON.parse(data);
    for (c in cols){
      collections[cols[c].id] = cols[c];
    }
  }}); 

  var generators_url = "/tacc/api.php?path=/generators/";
  var gens;
  $.ajax({url:generators_url, async: false, success: function(data){
    generators = new Object();
    gens = JSON.parse(data);
    for (g in gens){
      generators[gens[g].id] = gens[g];
    }
  }});
}


function new_collection(){

  $.ajax({url:"/tacc/api.php?path=/collections/", type:"POST", 
  success:function(data){
    collection = JSON.parse(data);
    new_url   = "collection_edit.php?id=" + collection.id;
    window.location = new_url;
  },
  error: function(data){
    alert("Could not create new collection");
  }
  });
}

</script>
<style type="text/css">
th,td{
	padding:.25em 1em .25em 1em;
}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Test Problem Server</title>
</head>
<body onload="start()">
<div id="wrapper">
  <div id="titlebar">
    <?php include("../includes/templating/title.inc.php"); ?>
  </div>
  <div id="menu">
    <?php include("../includes/templating/adminmenu.inc.php"); ?>
   </div>	
   <div id="content">
     <h1>Collection Management</h1>
     <input type="button" value="Create New Collection" onclick="new_collection()">
     <h2> Existing Collections</h2>
     <table id="collection_table"></table>
   </div>
   <div id="footer">
   </div>
</div>
</body>
</html>

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
  var table = $("#generator_table");
  var columns = ["Name","Collection","Script"];
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
  for (id in generators){
    insert_generator(generators[id]);  
  }
}

function insert_generator(generator){
  var col     = collections[generator.collection_id];
  var table   = $("#generator_table");
  var row     = $("<tr>");
  row.appendTo(table);

  var name_td = $("<td>");
  var glink    = $("<a>");
  glink.attr("href", "/admin/generators/" + generator.id + "/edit");
  glink.text(generator.name);
  name_td.append(glink);
  name_td.appendTo(row);

  var col_td  = $("<td>");
  if(col != undefined){
    var clink = $("<a>");
    clink.attr("href", "/admin/collections/" + col.id + "/edit");
    clink.text(col.name);
    col_td.append(clink);
  }else{
    col_td.text("None");
  }
  col_td.appendTo(row);
  
  var script_td = $("<td>");
  script_td.text(generator.script);
  script_td.appendTo(row);
 
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


function new_generator(){
  $.ajax({url:"/tacc/api.php?path=/generators/", type:"POST", 
  success:function(data){
    generator = JSON.parse(data);
    new_url   = "generator_edit.php?id=" + generator.id;
    window.location = new_url;
  },
  error: function(data){
    alert("Could not create new generator");
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
     <h1>Generator Management</h1>
     <input type="button" value="Create New Generator" onclick="new_generator()">
     <h2> Existing Generators</h2>
     <table id="generator_table"></table>
   </div>
   <div id="footer">
   </div>
</div>
</body>
</html>

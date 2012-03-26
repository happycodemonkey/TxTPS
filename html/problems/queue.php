<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link  rel="stylesheet" type="text/css" href="/css/main.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Test Problem Server</title>
<script src="http://www.google.com/jsapi?key=ABQIAAAAFm_alaZoE-oHFncIiaLcIxSvXgVckI6g_NvjnVRrpDD27K_tUxThinip_QAXOlx_WYg32QPVCEBh5A" type="text/javascript"></script>
<script>

collections = new Object; //keyed by ID
generators  = new Object; //keyed by ID

function start(){
  load_references();
  load();
  var frequency = 10; //in seconds
  setInterval(load,frequency * 1000);
}


function load_references(){
  $.getJSON("/tacc/api.php?path=/collections/",function(c_list){
    for (ci in c_list){
      var c = c_list[ci];
      var id = c["id"];
      collections["" + id] = c;
    }
  });


  $.getJSON("/tacc/api.php?path=/generators/",function(g_list){
    for (gi in g_list){
      var g = g_list[gi];
      var id = g["id"];
      generators[ "" + id] = g;
    }
  });

}

function load(){
  var base_url = "/tacc/api.php?path=/problems/";
  var states = ["queued", "processing", "completed", "hold", "error"];
  
 
  for(si in states){
    var s = states[si];
    $("#" + s).empty();
    //load all queued
    var url = base_url + "?status=" + s;
    console.log("Watching " + url);
    $.getJSON(url ,function(problems){
   
      if(problems.length < 1){ 
        return;
      } 

      var state = problems[0].status;
      console.log(url,problems);
      var td = $("#" + state);
      td.css("verticalAlign","top");
      var table = $("<table>");
      table.appendTo(td);
      var tr = $("<tr>");
      tr.append("<th>Collection</th><th>Generator</th><th>ID</th>");
      tr.appendTo(table);
      for(i in problems){
        var p    = problems[i]; 
        var generator = generators[p["generator_id"]];
        console.log(generator);
	var collection = collections[generator["collection_id"]];
        tr = $("<tr>");
        tr.appendTo(table);
        var collection_name = $("<td>");
	tr.append(collection_name);
        var generator_name  = $("<td>");
        tr.append(generator_name);
        var problem_id      = $("<td>");
        tr.append(problem_id);

        var link;
	link = $("<a>");
        link.attr("href","build.php#collection," + collection.id);
        link.text(collection.name);
        link.appendTo(collection_name);

	link = $("<a>");
        link.attr("href","build.php#generator," + generator.id);
        link.text(generator.name);
        link.appendTo(generator_name);

        link = $("<a>");
        link.attr("href","matrix.php?id=" + p.identifier);
        link.text(p.identifier);
        link.appendTo(problem_id);

      }
    });
  }
}

google.load("jquery", "1");
google.load("jqueryui", "1");
google.setOnLoadCallback(start); 

</script>
</head>
<body>
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
		<h1>TxTPS Queue</h1>
                <table>
                  <tr>
                    <th>Queued</th>
		    <th>Processing</th>
		    <th>Completed</th>
		    <th>Hold</th>
		    <th>Error</th>
		  </tr>
		  <tr>
		    <td  style="width:20em" class="queue" id="queued"></div>
		    <td  style="width:20em" class="queue" id="processing"></div>
		    <td  style="width:20em" class="queue" id="completed"></div>
		    <td  style="width:20em" class="queue" id="hold"></div>
		    <td  style="width:20em" class="queue" id="error"></div>
		  </tr>
		</table>
	</div>
	<div id="footer">
	</div>
</div>
</body>
</html>

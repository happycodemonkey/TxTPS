<?php 


require_once("../includes/db/common.inc.php"); 
require_once("../includes/db/tags.inc.php"); 

?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<link  rel="stylesheet" type="text/css" href="/css/main.css" />
<link  rel="stylesheet" type="text/css" href="/niftyCube.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Test Problem Server</title>
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
		<?php include("./submenu.inc.php");?>
	</div>
	<div id="content">
		<h1>Keyword and Tag Search</h1>
	<p>Search the descriptions for matrices, generators, and collections. <a href="advanced.php">Search by matrix statistics</a></p> 


	 <form>
         <table>
	<tr><td><label for="keyword">Keyword:</label></td>
	 <td><input size="60"  onClick="if(this.value == 'Tag or Keywords...'){this.value='';}" style="border:none;background-color:#EEE;color:#0CF;"  type="text" name="keyword" id="keyword" value="Enter a Keyword..."/></td>
        </table>
        <p><input style="float:right;clear:both;font-size:1em;" type="submit" value="Search"></p>
        </form>
			

	<?php
	if(isset($_REQUEST['keyword']) AND strlen(trim($_REQUEST['keyword'])) > 0){

	  $tag = $_REQUEST['keyword'];

	  echo "<h2>Collections</h2>";

	  $collections = tags_collection_search($tag);

	  if(count($collections)< 1){
	    echo "<p>None Found</p>";
	  }
	  
	  echo "<ol>";
	  foreach($collections as $collection){
	    echo "<li><a href=\"/matrices/collection.php?id=${collection['id']}\">${collection['name']}</a></li>";
	  }
	  echo "</ol>";


	  echo "<h2>Generators</h2>";

	  $generators = tags_generator_search($tag);

	  if(count($generators)< 1){
	    echo "<p>None Found</p>";
	  }

	  echo "<ol>";
	  foreach($generators as $generator){
	    echo "<li><a href=\"/matrices/generator.php?id=${generator['id']}\">${generator['name']}</a></li>";
	  }
	  echo "</ol>";

	  echo "<h2>Matrices</h2>";

	  $matrices = tags_product_search($tag);

	  if(count($matrices)< 1){
	    echo "<p>None Found</p>";
	  }



	}

       ?>
	</div>
	<div id="footer">
	</div>
</div>
</body>
</html>

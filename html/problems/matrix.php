<?
include_once("../includes/db/data.inc.php");
include_once("../includes/db/generators.inc.php");

$id = substr($_REQUEST['id'],0,6);

if(isset($_REQUEST['start']) && is_numeric($_REQUEST['start'])){
  $start = $_REQUEST['start'];
 }
$info = product_get($id);
$files = product_file_list($info['id']);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
<script type="text/javascript" src="/niftycube.js"></script>
<script type="text/javascript">
  window.onload=function(){
  Nifty("div#menu li","top");
  Nifty("div.column","all");
}
</script>
<link  rel="stylesheet" type="text/css" href="/css/main.css" />
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
  <?php include("./submenu.inc.php"); ?>
</div>
<div id="content">
  <?php
if($info){
  ?>
  <h1>Matrix # <?php echo $id; ?></h1>
  <div>
  
  <div class="column">
  <h2>Demographics</h2>
  <table>
  <tr>
  <td><b>Collection</b></td>
  <td><a href="collection.php?id=<?php echo $info['collection_id']?>"><?php echo $info['col_name'] ?></a></td>
  </tr>
  <tr>
  <td><b>Generator</b></td>
  <td><a href="generator.php?id=<?php echo $info['generator_id']?>"><?php echo $info['gen_name'] ?></td>
  </tr>
  </table>
  <table>
  <tr><td><b>Collection Description</b></td></tr>
  <tr><td><?php 
  $description = $info['col_desc'];
  $description = eregi_replace("<script.*>.*</script>", "", $description);
  $description = strip_tags($description);
  echo substr($description, 0, 500) . "..."; 
  ?></td></tr>
  </table> 

  <table>
  <tr><td><b>Generator Description</b></td></tr>
  <tr><td><?php echo strip_tags($info['gen_desc'], "<p><a>"); ?></td></tr>
  </table>


  <table>
  <tr><td><b>Problem Description</b></td></tr>
  <tr><td><?php echo $info['description']; ?></td></tr>
  </table>
 

  </div>

  <?php
  //<h2>Plots, Graphics, Visualizations</h2>
  //<img src="plot.php?type=structural&matrix_id=
  ?>
  
  <div class="column">
  <h2>Arguments</h2>
  <?php
  $args = unserialize($info['arguments']);
  $arg_list = generator_arguments_get($info['generator_id']);
  $arg_info = array();
  foreach($arg_list as $item){
    $arg_info[$item['variable']] = $item;
  }

  //  var_dump($arg_info);
  
  if(count($args) > 0){
    echo "<table>\n";
    echo "<th>Name</th>\n";
    echo "<th>Value</th>\n";
    echo "<th>Default</th>\n";
    foreach($args as $key=>$value){
      echo "<tr>\n";
      echo '<td><b><acronym title="' . $arg_info[$key]['description'] . '">' . $arg_info[$key]['name'] ."</b></td>\n";
      echo "<td>$value</td>\n";
      echo "<td>None</td>\n";
      echo "</tr>\n";
    }
    echo "</table>\n";

  echo '<p>';
  ?>

    These build arguments can be used as a base to generate a new matrix of the same type. 
    Click on the link below and the arguments above will automaticlly pulled from the database for use as a template. 

  <?php
  echo '</p>';
  
  echo '<p>';
  echo '<a href="generator.php?id=' .$info['generator_id'] . '&from=' . $_GET['id'] .  '">Duplicate</a>';
  echo '</p>';


  }else{
    echo "<p>No arguments were provided</p>";
  }
  ?>
 </div>

  <div class="column">
  <h2>Output Files</h2>
  <table>
  <tr>
  <th>Name</th>
  <th>Size</th>
  <th>Type</th>
  </tr>
  <?php
	
  foreach($files as $file){

    if($file['name'] == "stdout.log"){
      continue;
    }

    if($file['name'] == "stderr.log"){
      continue;
    }

    echo "<tr>";
    echo "<td><a href=\"download.php?fileid=" . $file['id']  ."\">" . $file['name'] . "</a></td>";
    echo "<td>" . round(($file['size']/1024),2) . "KB</td>";
    echo "<td>" . $file['type'] . "</td>";
    echo "</tr>";
  }
  echo "</table>";
  ?>
  </div>


  <div class="column">
  <h2>Download Archive</h2>
  <p>All archives are compressed using .tar.gz. You may need to download a seperate utility in order to extract these files</p>
  <div>
  <form action="download.php">
  <label for="convert_mode"><b>Format</b></label>
  <select id="convert_mode" name="mode">
  <option value="NONE">Original Format</option>
  <?php
  //<option value="PET2MM">Convert to Matrix Market</option>
  //<option value="MM2PET">Convert to PETSc</option>
  ?></select>
  <br>
  <br>
  <input style="margin:1em;" type="submit" value="Download Archive">
  <input type="hidden" value="<?php echo $info['identifier']?>" name="identifier">
  </form>
  </div>

  </div>

  <div style="display:block:visibility:none;clear:both;bg-color:red;"></div>
  
  <?php
  foreach($files as $file){
    $img = "/test/images/plots/". $file['id'] . "_sparsity.png";
    if(file_exists("/var/www/html/" . $img)){
      ?>
      <div class="column" style="padding:3em;">
	<h2><?php echo $file['name'];?> - Sparsity</h2>
	<a href="<?php echo $img?>"><img width="250" src="<?php echo $img?>"></a>
					  <p>&nbsp;</p>
      </div>
  
  <?php
    }//end if statement
  }//end file loop
  ?>
  <div style="display:block:visibility:none;clear:both;bg-color:red;"></div>
  <?php
  
  $ident = $id;
  $query = "SELECT anamod_data.*,product.identifier as product_identifier, file.name as file_name FROM anamod_data LEFT JOIN file ON anamod_data.file_id = file.id LEFT JOIN product ON product.id = file.product_id WHERE product.identifier = '$ident' ORDER BY file.name";

  //echo $query;
  $files =  db_query($query, true);

  if(count($files) == 0){
    $width = 30;
  }else{
    $width = count($files) * 25; // 25 em per file column
  }

  ?>
  <?php

  if(count($files) > 0){
    
    foreach($files as $statistics){
      ?>
      
      <?php
      $group = "";
      foreach($statistics as $key=>$value){
	
	//remove file foreign keys
	if( strpos ($key, "file") === 0){    
	  continue;
	}

	  //remove product foreign keys
	if( strpos ($key, "product") === 0){
	  continue;
	}


	$key_parts = preg_split("/-/" , $key, 2);
	
	
	if($group != $key_parts[0]){
	  if($group != ""){
	    echo "</table>";
	    echo "</div>";
	  }
	  ?>
	    <div class="column" style="display:block;width:<?php echo 20 ?>em; height:50em; max-width:100em; float:left;">

	       
	  <?php
	  $group = $key_parts[0];
	  
	  echo "<h2>" . $statistics['file_name'] . " - " . ucfirst($group) .  "</h2>";
	  echo '<table>';
	  echo '<th>Key</th><th>Value</th></tr>';
	    
	
	}

	  $remaining = $key_parts[1];
	  echo "<tr><td>$remaining</td><td><em><nobr>$value</pre></nobr></td></tr>";
	  
      }
      //close off the file
      echo "</table>";
      echo "</div>";
      
  }

  }else{
    echo "<p> No statistics found </p>";
  }
  ?>

  </div>


  <?php
  
  }else{
  echo "<h1>Error</h1>\n";
  echo "<p>Matrix # $id could not be found in the database.</p>\n";
 }
?>
</div>
</div>
<div id="footer"></div>
</div>
</body>
</html>
 
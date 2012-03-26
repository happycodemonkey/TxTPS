<?
include_once("../includes/db/data.inc.php");
include_once("../includes/db/generators.inc.php");
require_once("../includes/classes/Security.class.php");

$id = substr($_REQUEST['identifier'],0,6);
if(isset($_REQUEST['start']) && is_numeric($_REQUEST['start'])){
  $start = $_REQUEST['start'];
 }
$info = product_get($id);
$files = product_file_list($info['id']);
$collection_list = generator_collection_list();



$security = Security::getInstance();
if(!$security->isLoggedIn() || $security->getClassName() !== "Admin"){
  Security::forward("../login.php");
 }



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
	<div id="menu">
		<?php include("../includes/templating/adminmenu.inc.php"); ?>
	</div>
<div id="content">
  <?php
if($info){
  
  $status = "";

  if($info['error'] == false){
    
    if($info['hold'] == false){
      
      if(!is_null($info['created'])){
	
	$status = "Built"; 
	
      }else{
	$status = "Queued";
      }

    }else{
      $status = "Held";
    }

  }else{
    $status = "Error";
  }


  
  ?>
  <h1>Manage Matrix # <?php echo $id; ?></h1>
  <div>
  
  <div class="column">
  <h2>Status</h2>
  <table>
  <tr>
  <td><b>Collection</b></td>
  <td><a href="/matrices/collection.php?id=<?php echo $info['collection_id']?>"><?php echo $info['col_name'] ?></a></td>
  </tr>
  <tr>
  <td><b>Generator</b></td>
  <td><a href="/matrices/generator.php?id=<?php echo $info['generator_id']?>"><?php echo $info['gen_name'] ?></td>
  </tr>
  <tr>
  <td><b>Status</b></td>
  <td><?php echo $status; ?></td>
  </tr>
  <tr>
  <td><b>Creation</b></td>
  <td><?php echo $info['created']; ?></td>
  </tr>
  </table>

  <h2>Manage</h2>

  <table>
  <tr>
  <td><a href="/matrices/matrix.php?id=<?php echo $info['identifier']; ?>">View Problem</a></td>  
  <td><a href="problem_modify.php?action=hold&identifier=<?php echo $info['identifier']; ?>">Hold Problem</a></td>
  </tr>
  
  <tr>
  <td><a href="problem_modify.php?action=rebuild&identifier=<?php echo $info['identifier']; ?>">Rebuild Problem</a></td>  
  <td><a href="problem_modify.php?action=unhold&identifier=<?php echo $info['identifier']; ?>">Unhold Problem</a></td>
  </tr>
  </table>


  <h2>Delete</h2>
  <p><a href="problem_modify.php?action=delete&identifier=<?php echo $info['identifier']; ?>">Delete Problem</a></p>

  <p>(Warning, this cannot be undone)</p>

  </div>


  
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
  echo '<a href="/matrices/generator.php?id=' .$info['generator_id'] . '&from=' . $info['identifier'] .  '">Duplicate</a>';
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
    echo "<tr>";
    echo "<td><a href=\"/matrices/download.php?fileid=" . $file['id']  ."\">" . $file['name'] . "</a></td>";
    echo "<td>" . round(($file['size']/1024),2) . "KB</td>";
    echo "<td>" . $file['type'] . "</td>";
    echo "</tr>";
  }
  echo "</table>";
  ?>
  </div>

  <div class="column">
  <h2>stdout</h2>
  <pre>
  <?php
  $stdout = "/data/storage/" . $info['identifier'] . "/stdout.log";
  if(!file_exists($stdout)){
       echo "<b>stdout file not found</b>";
       
  }else if(filesize($stdout) == 0){
       echo "<b>stdout exists, but is empty<b>";
  }else{
     
       readfile($stdout);
      
     }
  ?>
  </pre>
  </div>


  <div class="column" style="min-width:30em">
  <h2>stderr</h2>
  <pre>
  <?php
  $stderr = "/data/storage/" . $info['identifier'] . "/stderr.log";
     if(!file_exists($stderr)){
       
       echo "<b>stderr file not found</b>";
       
     }else if(filesize($stderr) == 0){
       echo "<b>stderr exists, but is empty<b>";
     }else{
       readfile($stderr);
    
     }
  ?>
  </pre>
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
 
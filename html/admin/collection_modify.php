<?php
include_once("../includes/util/installer.inc.php");
include_once("../includes/db/generators.inc.php");
require_once("../includes/classes/Collection.class.php");
require_once("../includes/classes/Generator.class.php");
require_once("../includes/classes/Security.class.php");


$security = Security::getInstance();
if(!$security->isLoggedIn() || $security->getClassName() !== "Admin"){
  Security::forward("../login.php");
}

$action = $_REQUEST['action'];

$title = "";
$content ="";



switch($action){

 case "edit":
   $title = "Modify Collection";
   break;
 case "reinstall":
   $title = "Reinstall Collection";
  break;

 case "delete":
   $title = "Record Deletion";
 break;

 default:
 case "install":
   $title = "Install New Collection";
 break;
}


function tmpdir($path, $prefix)
{
        // Use PHP's tmpfile function to create a temporary
        // directory name. Delete the file and keep the name.
        $tempname = tempnam($path,$prefix);
        if (!$tempname)
                return false;

        if (!unlink($tempname))
                return false;

        // Create the temporary directory and returns its name.
        if (mkdir($tempname))
                return $tempname;

        return false;
}

function reinstall(){
  $step = $_REQUEST['step'];
  $id =   $_REQUEST['id'];

  if($step == 0 || !isset($step)){
  ?>
      <p> Are you sure you want to reinstall this collection? </p>
      <p>Note: All matrices produced with existing generators <em>will</em> be saved.</p>
      <p>
      <form>
      <input type="hidden" name="action" value="reinstall">
      <input type="hidden"  name="step"  value="1">
      <input type="hidden" name="id" value="<?php echo $id;?>">
      <input type="submit" value="Yes, Reinstall">
      </form>
      </p>
      <p>
      <form method="get">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" value="<?php echo $id;?>">
      <input type="submit" value="Cancel">
      </form>
      </p>
   <?php      
      }elseif($step == 1 && strlen($id) > 0){
      echo "<p>Loading Collection<p>";
      $collection = Collection::loadByID($id);
      echo "<p>Reinstalling collection with ID: $id</p>";
      $collection->reinstall($id, $error);

    
      echo "<p> The collection has been reinstalled</p>";
      echo "<a href=\"collection.php\">Continue</a>";
  }else{
    echo "<p> Your request could not be processed</p>";
  }
  
}

function install(){


  $step = $_REQUEST['step'];
  $html = "";

  if($step == 0 || !isset($step) || strlen($_REQUEST['class']) < 1){ //display form for uploading
   ?>
   <p>
	The genererator files including the PHP adapters should be in a directory under /data/install.
	This directory must be read/exec-able by apache. Please specify the file location
	of the main collection class below. 
   </p>
   <table><form method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="install">
    <input type="hidden" name="step" value="1">
   <tr>
     <td>Collection Class Location</td><td><input value ="/data/install" type="text" name="class" value="S"></td>
   </tr>
   <tr>
   <td><input type="submit"  value="Next"></td>
   <td></td>
   </tr>
   </form>
   </table>
   <?php
  }
  elseif($step == 1){ //confirm installation
  //validate
  $classFile = $_REQUEST['class'];
  $valid = ('.php' == substr($classFile, strlen($classFile)-4,strlen($classFile)));
  if(!$valid){
	echo "<p> <b>$classFile</b> is not a valid PHP class file </p>";
	echo "<p> Class files should end in '.php', but this ends in " . substr($classFile, strlen($classFile)-4,strlen($classFile)) . "</p>";
	return;
  }
  $valid = file_exists($classFile);
  if(!$valid){
	echo "<p> <b>$classFile</b> does not exist </p>";
	return;
  }
  $valid = is_readable($classFile);
  if(!$valid){
	echo "<p> <b>$classFile</b> is not readable </p>";
	return;
  }
  
  //load class
  require_once($classFile);
  $className = basename($classFile);
  $className = substr($className, 0, strlen($className)-4);
  $collection = call_user_func(array($className,"getInstance"));
    
   ?>
    <p>Collection Information</p>
	<table>
		<tr>
			<th>Name</th>
			<th>Class Name</th>
		</tr>
		<tr>
			<td><?php echo $collection->getName();?></td>
			<td><?php echo $className; ?></td>
		</tr>
	</table>
   <h2>Generators</h2>
   <?php
   


    foreach($collection->getGenerators() as $generator){
		echo "<div style=\"border-top:solid 2px grey\">";
			?>
			<p>Generator Information</p>
			<table width="100%">
			<tr>
				<th>Generator Name</th>
				<th>Generator Class</th>
				<th>Output Format</th>					
			</tr>
			<tr>
				<td><?php echo $generator->getName()?></td>
				<td><?php echo get_class($generator);?></td>
				<td><?php echo $generator->getFormat()?></td>
			</tr>
			</table>
			
			<p>Argument Table:</p>
			<table width="100%">
			<tr>
				<th>Param</th>
				<th>Title</th>
				<th>Type</th>
				<th>Description</th>
				<th>Optional</th>
				
			</tr>
			<?php
			foreach($generator->getArgs() as $arg){
				echo "<tr>";
				echo "<td>".$arg[0]."</td>";
				echo "<td>" . $arg[1]."</td>";
				echo "<td>" . $arg[2] ."</td>";
				echo "<td>" . $arg[3] ."</td>";
				echo "<td>" .(($arg[4])?"True":"False") ."</td>";
				echo "</tr>";
			}
			echo "</table>";
		echo "</div>";
	}
   ?>
   
   <p>
   Are you sure you want to install this collection?
   </p>
   <table><form method="post">
   <input type="hidden" name="action" value="install">
   <input type="hidden" name="step" value="2">
   <input type="hidden" name="class" value="<?php echo $_REQUEST['class']; ?>">
   <tr>
   <tr>
   <td>
    <input type="submit"  name="confirm" value="Install">
   </td>
   <td>
    <input type="submit" name="confirm" value="Cancel">
   </td>
   </tr>
   </form>
   </table>
   <?php
	}elseif($step == 2){ //install collection. note that collection was installed

		$classFile = $_REQUEST['class'];
		$valid = '.php' == substr($classFile, strlen($classFile)-4,strlen($classFile));
		if(!$valid){
			echo "<p> <b>$classFile</b> is not a valid PHP class file </p>";
			return;
		}
		$valid = file_exists($classFile);
		if(!$valid){
			echo "<p> <b>$classFile</b> does not exist </p>";
			return;
		}
		$valid = is_readable($classFile);
		if(!$valid){
			echo "<p> <b>$classFile</b> is not readable </p>";
			return;
		}

		//load class	
		require_once($classFile);
		echo $className = basename($classFile);
		$className = substr($className, 0, strlen($className)-4);
		$collection = call_user_func(array($className,"getInstance"));
		
		if($_REQUEST['confirm'] !== "Install"){
			rmdir($tmp_dir);
			?>
			<p> The collection was not installed. <a href="index.php">Click here</a> to return to the admin menu.</p>
			<?php
		}else{
			$errorMsg;
			if($collection->install($errorMsg, $classFile)){
				?>
					<p> The collection was successfully installed. <p>
					</p><a href="index.php">Click here</a> to return to the admin menu.</p>
				<?php
			}else{
				?>
					<p> Installation of the collection <b>failed</b> with the following error message:</p>
					<p><b><?php echo $errorMsg; ?></b></p>
					</p><a href="index.php">Click here</a> to return to the admin menu.</p>
				<?php
			}
		}
	}
}


function delete(){
	$step = 0;
	$id = $_REQUEST['id'];
	if(isset($_REQUEST['step']) AND is_numeric($_REQUEST['step'])){
		$step = $_REQUEST['step'];
	}
	if($step == 0){
	$info = generator_collection_get($id);
	?>
		<form method="post">
		<input type="hidden" name="action" value="delete">
		<input type="hidden" name="step" value="1">
		<input type="hidden" name="id"	value="<?php echo $id?>">
		<p>Are you sure you want to delete the <?php echo$info['name']?> collection?</p>
		<p><input type="submit" name="submit" value="Delete"> <input type="submit" name="submit" value="Cancel"></p>
		</form>
	<?php
	}elseif($step == 1){
		if($_REQUEST['submit'] == "Delete"){
			generator_collection_delete($id);
			?>
			<p>The collection was successfully deleted. <a href="collection.php">Click here</a> to continue.</p>
			<?php
		}else{
			?>
			<p>The collection was NOT deleted. <a href="collection.php">Click here</a> to continue.</p>
			<?php
		}
	}
}

function edit(){

  $step = $_REQUEST['step'];
  $id	= $_REQUEST['id'];

  if($step = 0 || !isset($step)){ //display form with record contents

	$record = generator_collection_get($id);
    ?>
   <p>
   Modify the collection below if needed, then click save to confirm changes.
   </p>
   <table><form method="post">
   <tr>
   <input type="hidden" name="action" value="edit">
   <input type="hidden" name="step" value="1">
   <input type="hidden" name="id"  value="<?php echo $id; ?>">
   <tr>
      <td>Name: </td>
      <td><input type="text" name="name" value="<?php echo $record['name'] ?>"></td>
   </tr>
   <tr>
     <td>Format ID: </td>
     <td> <input type="text" name="format" value="<?php echo $record['collectiontype_id']?>"></select></td>
   </tr>
   <tr>
     <td>Description:</td>
     <td><textarea name="description" rows="5" cols="40"><?php echo $record['description']?></textarea>
   </tr>
   <tr>
   <td><input type="submit"  value="Save"></td>
   <td></td>
   </tr>
   </form>
   </table>
   <p>
   <form method="post">
   <input type="hidden" name="action" value="delete">
   <input type="hidden" name="step" value="0">
   <input type="hidden"	name="id"   value="<?php echo $id?>">
   <input type="submit"  value="Delete Collection">
   </form>

   </p>
   <p>
   <form method="post">
   <input type="hidden" name="action" value="reinstall">
   <input type="hidden" name="step" value="0">
   <input type="hidden"	name="id"   value="<?php echo $id?>">
   <input type="submit"  value="Reinstall Collection">
   </form>
   </p>

   <?php
  }
  elseif($step = 1){ //update the collection

    generator_collection_update($_REQUEST['id'], $_REQUEST['name'], $_REQUEST['description']);
   ?>
   <p> The collection was updated successfully. <a href="collection.php">Continue</a></p>
   <?php
  }
  else{ //something bad happened
   ?>
   <p> Not entirely sure what happened. <a href="collection.php">Continue</a></p>
   <?php
  }

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Test Problem Server</title>
<head>
<link  rel="stylesheet" type="text/css" href="../css/main.css" />
<style type="text/css">
th,td{
	padding:.25em 1em .25em 1em;
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

</style>
</script>
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
                <h1><?php echo $title; ?></h1>
                <?php

		switch($action){

		  case "edit":
 		  edit();
  		  break;

		  case "delete":
   		  delete();
 		  break;

		  case "reinstall":
		    reinstall();
		    break;

 		  default:
 		  case "install":
   		  install();
		  break;
		}

		?>
	</div>
	<div id="footer">
	</div>
</div>
</body>
</html>

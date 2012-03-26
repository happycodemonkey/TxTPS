<?php 
include_once("../includes/db/generators.inc.php");
$collection_list = generator_collection_list();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<link rel="stylesheet" type="text/css" href="/css/main.css">
<style type="text/css">
th,td{
	padding:0 1em 0 1em;
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
  <div style="display:block;float:left;width:35em;">
  <h1>Step 1: Select Generator Group</h1>

		<div>
		
		</div>
		<?php 
		if($collection_list == false){
		  echo "<p><b>No generator groups found</b></p>";
		}else{
		?>

		<div>
			<table>
			<tbody><tr>
				<th style="padding:.25em 1em .25em 1em;">Group Name</th>
				
				<th style="padding:.25em 1em .25em 1em;">Generator List</th>
			</th></tr><tr>
			</tr>
			<?php
			foreach($collection_list as $row){
		    
		    //TODO Implement selection of additonal information in the DB code
			?>
			<tr>
				<td style="padding:.25em 1em .25em 1em;"><?php echo $row["name"]?></td>
				
				<td style="padding:.25em 1em .25em 1em;"><form method="get" action="collection.php"><input name="id" value="<?php echo $row['id']?>" type="hidden"><input value="List Generators" type="submit"></form></td>
			</tr>
			<?php 
			}//end for loop
			?>
		</tbody></table>

		</div>
		<?php
		}//end else
		?>
		</div>
	</div>
	<div id="footer">
	</div>
</div>
</body></html>

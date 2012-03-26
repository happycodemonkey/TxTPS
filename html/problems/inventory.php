<?
include_once("../includes/db/data.inc.php");
$product_list = null;
$start = 0;
$limit = 100000000;
$sort = "id";


$product_list = product_list($sort,$start,$limit);



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>


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
		<h1>Problems</h1>
		<?php
	       if(($product_list) !== false){
			?>
			<div>
				<table>
				<tbody><tr>
			                <th>Problem ID</th>
					<th>Collection</th>
					<th>Generator</th>
					<th>Creation/Status</th>
				</tr>
					<?php
					foreach($product_list as $row){
					?>
					<tr><?php
						if(isset($row["identifier"]) && strlen($row["identifier"]) > 0){
						 ?><td><a href="matrix.php?id=<?php echo $row["identifier"]?>"><?php echo $row["identifier"]?></a></td><?php
						}else{
						?><td>N/A</td><?php
						}
						?>
						<td><a href="collection.php?id=<?php echo $row['collection_id'] ?>"><?php echo $row["col_name"]?></a></td>
						<td><a href="generator.php?id=<?php echo $row['generator_id'] ?>"><?php echo $row["gen_name"]?></a></td>
						<?php
						if(isset($row["created"]) && strlen($row["created"]) > 0){
						 ?><td><?php echo $row["created"]?></td><?php
						}elseif($row["error"] == 1) {
					           echo "<td>Build Error</td>";
					         }elseif($row["hold"] == 1){
					           echo "<td>Held in queue</td>";     
					         }else{
					           echo "<td>Building</td>";
					         }
						?>
					</tr>
					<?php 
					}//end for loop
					?>
				</tbody></table>
			</div>
			<?php
		}else{//end of if(false)
			?>
			<div>
				<p><b>No matrices found</b></p>
			</div>
			<?php
		}//end of else
		?>
	</div>
	<div id="footer">
	</div>
</div>
</body>
</html>

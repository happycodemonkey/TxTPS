<?
include_once("../includes/db/data.inc.php");
require_once("../includes/classes/Security.class.php");

$product_list = null;
$start = 0;
$limit = 100000000;
$sort = "id";


$product_list = product_list($sort,$start,$limit);


$security = Security::getInstance();
if(!$security->isLoggedIn() || $security->getClassName() !== "Admin"){
  Security::forward("../login.php");
 }

$query = "SELECT * FROM news ORDER BY timestamp DESC";
$stories = db_query($query,true);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<script type="text/javascript" src="/niftycube.js"></script>
<script type="text/javascript">
  window.onload=function(){
  Nifty("div#menu li","top");
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
  <h1>Matrices </h1>
		<?php
	       if(($product_list) !== false){
			?>
			<div>
				<table>
				<tbody><tr>
			                <th>Matrix ID</th>
					<th>Collection</th>
					<th>Generator</th>
					<th>Creation/Status</th>
			                <th>Hold</th>
			                <th>Delete</th>
			                </tr>
					<?php
					foreach($product_list as $row){
					?>
					<tr><?php
						if(isset($row["identifier"]) && strlen($row["identifier"]) > 0){
						 ?><td><a href="problem_view.php?identifier=<?php echo $row["identifier"]?>"><?php echo $row["identifier"]?></a></td><?php
						}else{
						?><td>N/A</td><?php
						}
						?>
						<td><a href="/matrices/collection.php?id=<?php echo $row['collection_id'] ?>"><?php echo $row["col_name"]?></a></td>
						<td><a href="/matrices/generator.php?id=<?php echo $row['generator_id'] ?>"><?php echo $row["gen_name"]?></a></td>
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
					<td><a href="problem_modify.php?identifier=<?php echo $row['identifier']; ?>&action=hold">Hold</a></td>
					<td><a href="problem_modify.php?identifier=<?php echo $row['identifier']; ?>&action=delete">Delete</a></td>
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

<?
include_once("../includes/db/users.inc.php");
include_once("../includes/db/generators.inc.php");
include_once("../includes/db/data.inc.php");
require_once("../includes/classes/Security.class.php");


//make sure user is logged in and an admin
$security = Security::getInstance();
if(!$security->isLoggedIn() || $security->getClassName() !== "Admin"){
  Security::forward("../login.php");
 }



$max_rows = 6; //max number of rows to display for each segment

function decodeSize( $bytes ){
    $types = array( 'B', 'KB', 'MB', 'GB', 'TB' );
    for( $i = 0; $bytes >= 1024 && $i < ( count( $types ) -1 ); $bytes /= 1024, $i++ );
    return( round( $bytes, 2 ) . " " . $types[$i] );
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link  rel="stylesheet" type="text/css" href="../css/main.css" />
<style type="text/css">
th,td{
	padding:.125em .5em .125em .5em;
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
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Test Problem Server</title>
<script src="http://www.google.com/jsapi?key=ABQIAAAAFm_alaZoE-oHFncIiaLcIxSvXgVckI6g_NvjnVRrpDD27K_tUxThinip_QAXOlx_WYg32QPVCEBh5A" type="text/javascript"></script>
<script>

function start(){


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
	<div id="menu">
		<?php include("../includes/templating/adminmenu.inc.php"); ?>
	</div>
	<div id="content">
		<h1>Server Adminstration</h1>

		<div id="genforms">
			<div id="UserPanel">
				<div class="panelHeader">User Management</div>
				<div class="panelContent">
				     <ul>
					<li><a href="user.php">Users</a></li>
				     </ul> 
				</div>
			</div>
			<div id="GenPanel">
				<div class="panelHeader">Collections/Generators</div>
				<div class="panelContent">
				     <ul>
					<li><a href="collection.php">Collections</a></li>
				     	<li><a href="generator.php">Generators</a></li>
				     </ul>
				</div>
			</div>
			<div id="DataPanel">
				<div class="panelHeader">Data</div>
				<div class="panelContent">
				     <ul>
					<li><a href="problem.php">Problems</a></li>
				     </ul>
				</div>
			</div>
			<div id="HealthPanel">
				<div class="panelHeader">Server Health</div>
				<div class="panelContent">
				     <ul>
					<li><a href="health.php">Health</a></li>
				     </ul>
				</div>
			</div>
			<div id="MiscPanel">
				<div class="panelHeader">Miscellaneous</div>
				<div class="panelContent">
				</div>
			</div>
		</div>
	</div>
	<div id="footer">
	</div>
</div>
</body>
</html>

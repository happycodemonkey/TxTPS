<?php
require_once("./includes/classes/Security.class.php");

$security = Security::getInstance();


$security->logout();


Security::forward("/");

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<script type="text/javascript" src="niftycube.js"></script>
<script type="text/javascript">
  window.onload=function(){
  Nifty("div#menu li","top");
}
</script>

<link  rel="stylesheet" type="text/css" href="css/main.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Test Problem Server</title>
</head>
<body>
<div id="wrapper">
	<div id="titlebar">
		<?php include("./includes/templating/title.inc.php"); ?>
	</div>
	<div id="login">
		<?php require("./includes/templating/login.inc.php"); ?>
	</div>
	<div id="menu">
		<?php include("./includes/templating/menu.inc.php"); ?>
	</div>
	<div id="sidemenu">
		<ul>
			<li><a class="sidemenulink" href="#">Accounts</a>
			<li><a class="sidemenulink" href="#">Support</a>
			<li><a class="sidemenulink" href="#">Contact</a>
			<li><a class="sidemenulink" href="#">Formats</a>
		</ul>
	</div>
	<div id="content">
		<h1>Please Login</h1>
		<p>
		<?php
		if($failed){
			echo "<b>NOTICE: Either your email or password could not be verified.</b>";
		}
		?>		
		</p> 
		
		<p>
		Use the form on the right
		</p>
		
	</div> 
	<div id="footer">
	</div>
</div>
</body>
</html>

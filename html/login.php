<?php
$doc_root = getEnv("DOCUMENT_ROOT");
require_once($doc_root . "/includes/classes/Security.class.php");

//grab security object
$security = Security::getInstance();


$failed = false;
if(isset($_POST['email']) && isset($_POST['password'])){
	$failed = !$security->login($_POST['email'], trim($_POST['password']));


	$destination = $security->getReferer();

	
	if(isset($_POST['origin']) && strlen(trim($_POST['origin'])) > 0){
	  $destination = $_POST['origin'];
	}
	 
	
	if(!$failed)
	{
	    Security::forward($destination);
	}
}
if(isset($_REQUEST['logout'])){
	$security->logout();
}


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
		<?php include($doc_root . "/includes/templating/title.inc.php"); ?>
	</div>
	<div id="login">
		<?php require($doc_root . "/includes/templating/login.inc.php"); ?>
	</div> 
	<div id="menu">
		<?php include($doc_root . "/includes/templating/menu.inc.php"); ?>
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
		Use the form below
		</p>
		
		<form action="https://tps.tacc.utexas.edu/login.php" method="post">
		<p><input type="text" name="email" value="<?php echo $_POST['email']; ?>"></p>
		<p><input type="password" name="password" ></p>
		<input type="hidden" name="origin" value="<?php echo $destination; ?>">
                <p><input type="submit" value="Login"></p>
		</form>

		
	</div> 
	<div id="footer">
	</div>
</div>
</body>
</html>

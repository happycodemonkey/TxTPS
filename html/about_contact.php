<?php
require_once(dirname(__FILE__) . "/includes/classes/Security.class.php");
require_once(dirname(__FILE__) . "/includes/classes/User.class.php");
require_once("includes/util/recaptchalib.php");
require_once("includes/util/emailer.inc.php");

//state vars
$email_error = false;
$captcha_valid = false;
$message_error = false;
$sent = false;
	
//page vars
$subject = $_POST['subject'];
$message = $_POST['message'];
$email = $_POST['email'];
$country = $_POST['country'];
$affiliation = $_POST['affiliation'];
$userClass = 1;

if(isset($_POST['subject']) && isset($_POST['message']) 
   && isset($_POST['email'])){
	

  if(strlen($message) < 1){
    $message_error = true;
  }

  if(strlen($email) < 4){
    $email_error = true;
  }
	
	$captcha = recaptcha_check_answer (RECAPTCHA_PRIVATE_KEY,
							$_SERVER["REMOTE_ADDR"],
							$_POST["recaptcha_challenge_field"],
							$_POST["recaptcha_response_field"]);

	$validCaptcha = $captcha->is_valid;
	
	if ($validCaptcha) {
	  //send email
	  $sent = true;
	}
 }else {
}
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
		<?php include("includes/templating/title.inc.php"); ?>
	</div>
	<div id="login">
		<?php require("includes/templating/login.inc.php"); ?>
	</div>
	<div id="menu">
		<?php include("about/menu.inc.php"); ?>
	</div>
	<div id="sidemenu">
		<?php include ("about/submenu.inc.php"); ?>
	</div>
	<div id="content">
		<?php
		if($sent){
		?>
		  <h1>Thank You</h1>
		  <p>Your message has been sent. You can expect a response soon.</p>
		<?
		}
		else{
		?>
		<h1>Contact TxTPS</h1>
	       

		 
		 <?php

		    if($message_error){
		      echo "<p><b>Please fill in a message. This may not be left blank</b></p>";
		    }


		    if($email_error){
		      echo "<p><b>Please enter a valid email. This may not be left blank</b></p>";
		    }

		  ?>
		 


		 <p>Please fill in the form below. A valid email and a message  are required.</p>
		<form method="post">
			<table style="margin:1.5em;">
			<tr>
				<td><label for="email">E-Mail</label></td>
				<td><input type="text" id="email" name="email" size="42" value="<?php echo $email ?>"></td>
			</tr>
			<tr>
				<td><label for="subject">Subject</label></td>
				<td><input type="text" id="subject" name="subject" size="42" value="<?php echo $subject ?>"></td>
			</tr>
			<tr>
				<td><label for="message">Message</label></td>
				<td><textarea type="text" id="message" name="message" rows="10" cols="40"><?php echo $message ?></textarea></td>
			</tr>
			   
			</tbody>
			</table>
			<?php
			if (($email_error || $message_error) &&  !$captcha_valid)
			{
			  echo "<p><b>The code below must be entered exactly as it appears with <u>no</u> space.</b></p>";
			}
			?>
			
			<div style="margin-left:3em;">
			<p> Enter the two words below without a space</p>
			<?php
				$error = null;
				echo recaptcha_get_html(RECAPTCHA_PUBLIC_KEY, $error);
			?>
			</div>
			<br />
			<br />
			<input type="submit" value="Send" style="margin-left:0;">
		</form>
		<?php
		}//end default	
		?>
	</div>
	<div id="footer">
	</div>
</div>
</body>
</html>

<?php
include_once("../includes/db/users.inc.php");
//require_once("./includes/util/security.inc.php");
require_once("../includes/classes/Security.class.php");
require_once("../includes/classes/User.class.php");
require_once("../includes/classes/Email.class.php");
require_once("../includes/util/recaptchalib.php");


$email_valid = false;
$captcha_valid = false;
$error = false;
$message = "";

$captcha = recaptcha_check_answer (RECAPTCHA_PRIVATE_KEY,
				   $_SERVER["REMOTE_ADDR"],
				   $_POST["recaptcha_challenge_field"],
				   $_POST["recaptcha_response_field"]);
$captcha_valid = $captcha->is_valid;
if(count($_POST) > 0){
  if($captcha_valid != true){
    $error = true;
    $message = "Invalid CAPTCHA, please try again";
  }
  
  if($captcha_valid && isset($_POST['email'])){

  
    $user = User::loadByEmail($_POST['email']);
  
    if($user instanceof User){
      $email_valid = true;
    }else{
      $error = true;
      $message = "Invalid email, please try again";
    }
}

 }


if($email_valid && $captcha_valid){
  
  //reset password
  $password = Security::generatePassword(8);

  $user->setPassword($password);


  //send notification email
  $email = new PasswordResetEmail($user,$password);
  $email->send();
 }
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
		<h1>Reset Password</h1>
		<span style="color:red; font-weight:bold;"><?php if($error){ echo $message; } ?></span>
		<?php
		if($captcha_valid && $email_valid){
	 
		  echo "<p>Your password request has been logged and submitted. You should receive a new password shortly by email. </p>";
		} else { //end if
	
		  echo "<p>Enter the email you used for the account and complete the captcha below. A new password will be sent to you via email.</p>";
		  echo '<form method="post">';
		  echo '<table>';
		  echo "<tr>";
		  echo '<td>E-Mail</td>';
		  echo '<td><input type="text" name="email" value=""></td>';
		  echo "</tr>";
		  echo "</table>";
		  echo "<p> Enter the two words below without a space</p>";
		
		  $error = null;
                  echo recaptcha_get_html(RECAPTCHA_PUBLIC_KEY, $error);
               
		  echo '<p><input type="submit" value="Reset"></p>';
		  echo '</form>';
   
		}//end else
        ?>
        </div>
	<div id="footer">
	</div>
</div>
</body>
</html>

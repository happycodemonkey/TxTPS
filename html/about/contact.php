<?php
$doc_root = getEnv("DOCUMENT_ROOT");
require_once($doc_root . "/includes/classes/Security.class.php");
require_once($doc_root . "/includes/classes/User.class.php");
require_once($doc_root . "/includes/util/recaptchalib.php");
require_once($doc_root . "/includes/util/emailer.inc.php");
require_once($doc_root . "/includes/common_code.php");


//state vars
$email_error = false;
$captcha_valid = false;
$message_error = false;
$sent = false;
	
//page vars
$userClass = 1;
$subject = "";
$message = "";
$email = "";
$alert_message = "";

if (isset($_POST['subject']) && isset($_POST['message']) && isset($_POST['email'])) {
	$subject = $_POST['subject'];
	$message = $_POST['message'];
	$email = $_POST['email'];

	if(strlen($message) < 1) {
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
}

$smarty->assign('subject', $subject);
$smarty->assign('message', $message);
$smarty->assign('email', $email);

$error = null;
$captcha = recaptcha_get_html(RECAPTCHA_PUBLIC_KEY, $error);

if ($sent) {
	$alert_message = "<p style='color: green;'><b>Your message was sent.</b></p>";	
} else {
	if ($message_error) {
		$alert_message .= "<p style='color: red;'><b>Please fill in a message. This may not be left blank</b></p>";
	}

	if ($email_error) {
		$alert_message .= "<p style='color: red;'><b>Please enter a valid email. This may not be left blank</b></p>";
	}

	if (($email_error || $message_error) &&  !$captcha_valid) {
	  	$alert_message .= "<p style='color: red;'><b>The code below must be entered exactly as it appears with <u>no</u> space.</b></p>";
	}	
}

$smarty->assign('captcha', $captcha);
$smarty->assign('alert_message', $alert_message);
$smarty->display('tps_contact.tpl');
?>

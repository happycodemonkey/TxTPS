
<?php 

include_once("./User.class.php");
include_once("./Email.class.php");


$user = User::loadByEmail("user@example");
$password = "test_password";
$id = 52;

$email = new AccountConfirmationEmail($user, $password);
$email->send();


$email = new PasswordResetEmail($user, $password);
$email->send();


$email = new JobRequestedEmail($user, $id);
$email->send();


$email = new JobCompleteEmail($user, $id);
$email->send();


$email = new JobFailureEmail($user, $id);
$email->send();

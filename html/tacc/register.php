
<?php
require_once(getEnv("DOCUMENT_ROOT") . "/includes/classes/User.class.php");

if(isset($_POST['email']) && isset($_POST['password'])){
	$new_user = new User();
	//@TODO: support extra params in reg field or profile?
	$user = $new_user->create(null, null, 1, $_POST['email'], null, null, $_POST['password']);

	if ($user) {
		echo "Success";
	}
}

?>

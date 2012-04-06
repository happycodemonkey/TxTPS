<?php
require_once(getEnv("DOCUMENT_ROOT") . "/includes/classes/Security.class.php");

//grab security object
$security = Security::getInstance();

$failed = false;
if(isset($_POST['email']) && isset($_POST['password'])){
	$loggedin = $security->login($_POST['email'], trim($_POST['password']));

	if($loggedin)
	{
	  $user_obj = new stdClass();
  	  $user_obj->email     = $security->getUser()->getEmail();
  	  $user_obj->firstname = $security->getUser()->getFirstName();
  	  $user_obj->lastname  = $security->getUser()->getLastName();
  	  $user_obj->id        = $security->getUser()->getID();
	  $user_obj->admin     = ($security->getUser()->getClassName() == "Admin");
  	  $user = json_encode($user_obj);
	  echo $user;
	}else{
	    header($_SERVER["SERVER_PROTOCOL"]. " 401");
	    echo "ERROR";
	}
}else{
  if($security->isLoggedIn()){
    $user_obj = new stdClass();
    $user_obj->email     = $security->getUser()->getEmail();
    $user_obj->firstname = $security->getUser()->getFirstName();
    $user_obj->lastname  = $security->getUser()->getLastName();
    $user_obj->id        = $security->getUser()->getID();
    $user_obj->admin     = ($security->getUser()->getClassName() == "Admin");
    $user = json_encode($user_obj);
    echo $user;
  }
}

?>

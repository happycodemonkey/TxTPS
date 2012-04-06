<?php
$dir_root = getEnv("DOCUMENT_ROOT");
//security 
require_once($dir_root . "/includes/classes/Security.class.php");
$security = Security::getInstance();
$user = null;
if($security->isLoggedIn()){
  $user_obj = new stdClass();
  $user_obj->email     = $security->getUser()->getEmail();
  $user_obj->firstname = $security->getUser()->getFirstName();
  $user_obj->lastname  = $security->getUser()->getLastName();
  $user_obj->id        = $security->getUser()->getID();
  $user_obj->admin     = ($security->getUser()->getClassName() == "Admin");
  $user = json_encode($user_obj);
}else{
  $user = null;
}

// put full path to Smarty.class.php
require($dir_root . '/apps/Smarty/Smarty.class.php');
$smarty = new Smarty();
$smarty->setTemplateDir($dir_root . '/smarty/templates');
$smarty->setCompileDir($dir_root . '/smarty/templates_c');
$smarty->setCacheDir($dir_root . '/smarty/cache');
$smarty->setConfigDir($dir_root . '/smarty/configs');

$smarty->assign("user",$user);
?>

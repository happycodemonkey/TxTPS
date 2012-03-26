<?php
//security 
require_once("includes/classes/Security.class.php");
$security = Security::getInstance();
$user = null;
if($security->isLoggedIn()){
  $user_obj = new stdClass();
  $user_obj->email     = $security->getUser()->getEmail();
  $user_obj->firstname = $security->getUser()->getFirstName();
  $user_obj->lastname  = $security->getUser()->getLastName();
  $user_obj->id        = $security->getUser()->getID();
  $user = json_encode($user_obj);
}else{
  $user = null;
}

// put full path to Smarty.class.php
require('/opt/apps/Smarty/Smarty.class.php');
$smarty = new Smarty();
$smarty->setTemplateDir('smarty/templates');
$smarty->setCompileDir('smarty/templates_c');
$smarty->setCacheDir('smarty/cache');
$smarty->setConfigDir('smarty/configs');


//get news
require_once("includes/db/common.inc.php");
$query = "SELECT * FROM news ORDER BY timestamp DESC";
$stories = db_query($query,true);
$featured_story = $stories[0];

$smarty->assign('user', $user);
$smarty->assign('featured_story', $featured_story);
$smarty->display('tps_index.tpl');



?>
<DIV CLASS="math"> 
  A = \left\lgroup\matrix{a_{11}& \cdots& a_{1m}\cr
                \vdots& \ddots& \vdots\cr
                a_{n1}& \cdots& a_{nm}\cr}\right\rgroup.
</DIV> 

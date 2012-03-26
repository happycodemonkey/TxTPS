<?php
include_once("../includes/db/data.inc.php");
include_once("../includes/db/generators.inc.php");
require_once("../includes/classes/Security.class.php");

$identifier = substr($_REQUEST['identifier'],0,6);
$info = product_get($identifier);
$files = product_file_list($info['id']);
$collection_list = generator_collection_list();
$action = $_REQUEST['action'];

 
$security = Security::getInstance();
if(!$security->isLoggedIn() || $security->getClassName() !== "Admin"){
  Security::forward("../login.php");
 }


if($action == "hold"){
  product_hold($info['id']);
}

if($action == "unhold"){
  product_unhold($info['id']);
 }

if($action == "delete"){
  product_delete($info['id']);
}

if($action == "rebuild"){
  product_rebuild($info['id']);
 }


//And finally forward
Security::forward("problem.php");





?>
<?php
$doc_root = getEnv("DOCUMENT_ROOT");
include_once($doc_root . "/includes/db/common.inc.php");
include_once("Handler.php");
require_once($doc_root . "/includes/classes/Security.class.php");

class UserHandler extends Handler{

  function filter($row){
   
    //exclude security tokens
    unset($row['password_hash']);
    unset($row['activation_key']);
    return $row;
  }
  
  function get($request){

    //only allow admin access
    $security = Security::getInstance();    
    if(!$security->isLoggedIn() || $security->getClassName() !== "Admin"){
      $this->display_error(401);
    }

    parent::get($request);
  }

  function getTableName(){
    return "user";
  }
}
?>

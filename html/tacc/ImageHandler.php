<?php
include_once("../includes/db/common.inc.php");
include_once("Handler.php");

class ImageHandler extends Handler{

  function filter($row){
    return $row;
  }

  public function getTableName(){
    return "image";
  }

}
?>
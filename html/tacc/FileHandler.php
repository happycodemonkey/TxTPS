<?php
include_once("../includes/db/common.inc.php");
include_once("Handler.php");

class FileHandler extends Handler{

  function filter($row){
    //add statistics if available 
    $statistics = null;
    $query      = "SELECT * FROM anamod_data WHERE file_id=${row['id']}";
    $result     = db_query($query,true);
    if(count($result) > 0){
      $statistics = $result[0];
    }
    $row['statistics'] = $statistics;
    return $row;
  }

  public function getTableName(){
    return "file";
  }

}
?>
<?php
include_once("../includes/db/common.inc.php");
include_once("Handler.php");

class StatisticHandler extends Handler{

  function filter($row){
    
    return $row;
  }

  public function getTableName(){
    return "anamod_data";
  }


  protected function get($request){

    if((strlen($request->id) > 0) && ($request->id[0] != "?")){
        
        $query = sprintf("SELECT * FROM %s WHERE file_id=%d", $this->getTableName(), $request->id);

        $item  = db_query($query,true);

        if(count($item) < 1){
          $item = array();  //return the empty set
        }else{
          $item = $item[0]; //remove the outer array
        }

        $item = $this->filter($item);

        $this->display_result(200,$item);
     }else{
       parent::get($request);
     }

  }
}
?>
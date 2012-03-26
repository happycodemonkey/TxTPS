<?php
include_once("../includes/db/common.inc.php");
include_once("Handler.php");
require_once("../includes/classes/Security.class.php");

class ScriptHandler extends Handler{

  function filter($row){
   
    //exclude security tokens
    unset($row['password_hash']);
    unset($row['activation_key']);
    return $row;
  }


  function put($request){

    // get JSON
    $json = file_get_contents('php://input');
    $data = json_decode($json);

    //insert/overwrite ID into data
    $data->id = $request->id;
    $this->update($data);

  }

  function post($request){

    //new 
    if(strlen($request->id) == 0){
    
      $json = file_get_contents('php://input');
      $data = json_decode($json);
      $this->create($data);
    
    //update
    }else{
      $this->put($request); //treat as a put
    }
  }

  private function create($data){


    $user_id = $this->checkAdmin();


    // screen for bad data
    //$this->normalize($data);
        
    //get query items
    $generator_id = $data->generator_id;
    
    // build query
    $query  = "INSERT INTO script ";
    $query .= " (generator_id,path) ";
    $query .= " VALUES (%d,'%s');";
    $query  = sprintf($query,
		      $data->generator_id,
		      $data->path);
   
    // create
    db_query($query,true);

    //fetch ID 
    $script_id = mysql_insert_id();
    
    //return new object
    $query    = "SELECT * FROM script WHERE id=%d";
    $query    = sprintf($query,$script_id);
    $script = db_query($query,true);
    $script = $argument[0]; // remove the list
    $this->display_result(201,$script);

  }

  private function update($data){
    
    $user_id = $this->checkAdmin();
    
    
    //find old argument
    $query = "SELECT * FROM script WHERE id=%d;";
    $query = sprintf($query,$data->id);


    $old   = db_query($query, true);
    if(count($old) < 1){
      $this->display_error(422, "Invalid Script ID ( ".$data->id." )");
    }
    $old = $old[0];

    //update fields
    $script_fields = array_keys($old);
    $changes         = get_object_vars($data);
    $new          = array();
    foreach($script_fields as $f){

      // this value is modified by the update
      if(array_key_exists($f,$changes)){
        $new[$f] = $changes[$f];
      }else{
        $new[$f] = $old[$f];
      }
    }


    //store update
    $id    = $new['id'];
    $query  = "UPDATE script " ;
    $query .= " SET path='%s', generator_id=%d ";
    $query .= " WHERE id=%d;";
    $query  = sprintf($query,
		      $new['path'],
		      $new['generator_id'],
		      $id);
   

    db_query($query);


    //return updated object
    $query = "SELECT * FROM script WHERE id=%d;";
    $query = sprintf($query,$id);
    $script = db_query($query,true);
    $script = $script[0];

    $this->display_result(200,$script);



  }

  private function checkAdmin(){
    //only allowed if logged in
    $security = Security::getInstance();
    if(!$security->isLoggedIn()){
      $this->display_error(401, "You must be an Admin");
    }else{
      $user_id = $security->getUser()->getID();
      return $user_id;
    }
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
    return "script";
  }
}
?>
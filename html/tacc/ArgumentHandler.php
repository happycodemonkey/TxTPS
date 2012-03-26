<?php
include_once("../includes/db/common.inc.php");
include_once("Handler.php");

class ArgumentHandler extends Handler{

  function filter($row){
    
    //unserialize options
    $row['options'] = unserialize($row['options']); 
    return $row;
  }

  public function getTableName(){
    return "arguments";
  }

  function delete($request){

    $user_id = $this->checkAdmin();

    if(strlen($request->id) > 0){
      $argument_id = $request->id + 0;
      
      $query = "SELECT * FROM arguments WHERE id=%d;";
      $query = sprintf($query, $argument_id);
      
      $argument = db_query($query,true);
      $argument = $argument[0]; //remove list
      
      $query = "DELETE FROM arguments WHERE id=%d LIMIT 1;";
      $query = sprintf($query, $argument_id);
      
      db_query($query,true);

      $this->display_result(200,$argument);
    
    }else{
      $this->display_error(400, "Invalid ID - " . "'$request->id'");
    }

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
    $query  = "INSERT INTO arguments ";
    $query .= " (generator_id,sequence,name,variable,type,description,optional,options,default_value) ";
    $query .= " VALUES (%d,%d,'%s','%s','%s','%s',%d,'%s','%s');";
    $query  = sprintf($query,
		      $data->generator_id,
		      $data->sequence,
		      $data->name,
		      $data->variable,
		      $data->type,
		      $data->description,
		      $data->optional,
		      $data->options,
		      $data->default_value);
   
    // create
    db_query($query,true);

    //fetch ID 
    $argument_id = mysql_insert_id();
    
    //return new object
    $query    = "SELECT * FROM arguments WHERE id=%d";
    $query    = sprintf($query,$argument_id);
    $argument = db_query($query,true);
    $argument = $argument[0]; // remove the list
    $this->display_result(201,$argument);

  }
  
  private function update($data){
    
    $user_id = $this->checkAdmin();
    
    
    //find old argument
    $query = "SELECT * FROM arguments WHERE id=%d;";
    $query = sprintf($query,$data->id);


    $old   = db_query($query, true);
    if(count($old) < 1){
      $this->display_error(422, "Invalid Argument ID");
    }
    $old = $old[0];




    //serialize options if needed
    if(array_key_exists('options',$data)){
      $options = $data->options;
      $data->options = serialize($options);
    }

    

    //update fields
    $argument_fields = array_keys($old);
    $changes         = get_object_vars($data);
    $new          = array();
    foreach($argument_fields as $f){

      // this value is modified by the update
      if(array_key_exists($f,$changes)){
        $new[$f] = $changes[$f];
      }else{
        $new[$f] = $old[$f];
      }
    }


    //store update
    $id    = $new['id'];
    $query  = "UPDATE arguments " ;
    $query .= " SET generator_id=%d,sequence=%d,name='%s',variable='%s',type='%s',description='%s',optional=%d,options='%s',default_value='%s' ";
    $query .= " WHERE id=%d;";
    $query  = sprintf($query,
		      $new['generator_id'],
		      $new['sequence'],
		      $new['name'],
		      $new['variable'],
		      $new['type'],
		      $new['description'],
		      $new['optional'],
		      $new['options'],
		      $new['default_value'],
		      $id);
   

    db_query($query);


    //return updated object
    $query = "SELECT * FROM arguments WHERE id=%d;";
    $query = sprintf($query,$id);
    $argument = db_query($query,true);
    $argument = $argument[0];

    $this->display_result(200,$argument);



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

  private function normalize($argument){
  

  }

}
?>
<?php
include_once("../includes/db/common.inc.php");
include_once("Handler.php");

class GeneratorHandler extends Handler{

  protected function getTableName(){
    return "generator";
  }

 

  function delete($request){

    $user_id = $this->checkAdmin();

    if(strlen($request->id) > 0){
      $generator_id = $request->id + 0;
      
      $query = "SELECT * FROM generator WHERE id=%d;";
      $query = sprintf($query, $generator_id);
      
      $generator = db_query($query,true);
      $generator = $generator[0]; //remove list
      
      $query = "DELETE FROM generator WHERE id=%d LIMIT 1;";
      $query = sprintf($query, $generator_id);
      
      db_query($query,true);

      $this->display_result(200,$generator);
   
    }else{
      $this->display_error(400, "Invalid ID - " . "'$request->id'");
    }

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


  function put($request){

  


    // get JSON
    $json = file_get_contents('php://input');
    $data = json_decode($json);

    //insert/overwrite ID into data
    $data->id = $request->id;
    $this->update($data);

  }

  private function update($data){
    
    $user_id = $this->checkAdmin();
    
    
    //find old generator
    $query = "SELECT * FROM generator WHERE id=%d;";
    $query = sprintf($query,$data->id);


    $old   = db_query($query, true);
    if(count($old) < 1){
      $this->display_error(422, "Invalid Generator ID");
    }
    $old = $old[0];

    

    //update fields
    $generator_fields= array_keys($old);
    $changes         = get_object_vars($data);
    $new          = array();
    foreach($generator_fields as $f){

      // this value is modified by the update
      if(array_key_exists($f,$changes)){
        $new[$f] = $changes[$f];
      }else{
        $new[$f] = $old[$f];
      }
    }


    //fix description
    $new['description'] = addslashes($new['description']);


    //store update
    $id    = $new['id'];
    $query  = "UPDATE generator " ;
    $query .= " SET script='%s',name='%s',description='%s',generatortype_id=%d, collection_id=%d, disabled=%d ";
    $query .= " WHERE id=%d;";
    $query  = sprintf($query,
		      $new['script'],
		      $new['name'],
		      $new['description'],
		      $new['generatortype_id'],
		      $new['collection_id'],
		      $new['disabled'],
		      $id);
   

    db_query($query);


    //return updated object
    $query = "SELECT * FROM generator WHERE id=%d;";
    $query = sprintf($query,$id);
    $generator = db_query($query,true);
    $generator = $generator[0];

    $this->display_result(200,$generator);


  }


  private function create($data){


    $user_id = $this->checkAdmin();


    // screen for bad data
    //$this->normalize($data);
        
    //get query items
    $generator_id = $data->generator_id;
    
    // build query
    $query  = "INSERT INTO generator ";
    $query .= " (collection_id,script,name,description,disabled) ";
    $query .= " VALUES (1,'/bin/true','New_Generator','No Description',0); ";
    
    // create with default values
    db_query($query,true);

    //fetch ID 
    $generator_id = mysql_insert_id();
    
    //update with provided information
    //$data->id = $generator_id;
    //$this->update($data);

    //return new object
    $query    = "SELECT * FROM generator WHERE id=%d";
    $query    = sprintf($query,$generator_id);
    $generator = db_query($query,true);
    $generator = $generator[0]; // remove the list
    $this->display_result(201,$generator);

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


}
?>
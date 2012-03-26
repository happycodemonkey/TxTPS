<?php
include_once("../includes/db/common.inc.php");
include_once("Handler.php");

class CollectionHandler extends Handler{

  public function filter($row){
    return $row;
  }

  public function getTableName(){
    return "collection";
  }

  function delete($request){

    $user_id = $this->checkAdmin();

    if(strlen($request->id) > 0){
      $collection_id = $request->id + 0;
      
      $query = "SELECT * FROM collection WHERE id=%d;";
      $query = sprintf($query, $collection_id);
      
      $collection = db_query($query,true);
      $collection = $collection[0]; //remove list
      
      $query = "DELETE FROM collection WHERE id=%d LIMIT 1;";
      $query = sprintf($query, $collection_id);
      
      db_query($query,true);

      $this->display_result(200,$collection);
   
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
    
    
    //find old collection
    $query = "SELECT * FROM collection WHERE id=%d;";
    $query = sprintf($query,$data->id);


    $old   = db_query($query, true);
    if(count($old) < 1){
      $this->display_error(422, "Invalid Collection ID");
    }
    $old = $old[0];

    

    //update fields
    $collection_fields = array_keys($old);
    $changes           = get_object_vars($data);
    $new               = array();
    foreach($collection_fields as $f){

      // this value is modified by the update
      if(array_key_exists($f,$changes)){
        $new[$f] = $changes[$f];
      }else{
        $new[$f] = $old[$f];
      }
    }


    //store update
    $id    = $new['id'];
    $query  = "UPDATE collection " ;
    $query .= " SET name='%s',description='%s'";
    $query .= " WHERE id=%d;";
    $query  = sprintf($query,
		      $new['name'],
		      $new['description'],
		      $id);
   

    db_query($query);


    //return updated object
    $query = "SELECT * FROM collection WHERE id=%d;";
    $query = sprintf($query,$id);
    $collection = db_query($query,true);
    $collection = $generator[0];

    $this->display_result(200,$collection);


  }


  private function create($data){


    $user_id = $this->checkAdmin();


    // screen for bad data
    //$this->normalize($data);
            
    // build query
    $query  = "INSERT INTO collection ";
    $query .= " (name,description) ";
    $query .= " VALUES ('New_Collection','No Description'); ";
   
    // create with default values
    db_query($query);

    //fetch ID 
    $collection_id = mysql_insert_id();
    
    //return new object
    $query      = "SELECT * FROM collection WHERE id=%d";
    $query      = sprintf($query,$collection_id);
    $collection = db_query($query,true);
    $collection = $collection[0]; // remove the list
    $this->display_result(201,$collection);

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
<?php
include_once("../includes/db/common.inc.php");
include_once("Handler.php");

class ProductHandler extends Handler{

  protected function getTableName(){
    return "product";
  }

  function put($request){

    $json = file_get_contents('php://input');
    $data = json_decode($json);
    $this->update($data);
  }


  function post($request){
  
    
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
    $user_id;
    $generator_id;
    $arguments;
    $identifier;

    //only allowed if logged in
    $security = Security::getInstance();
    if(!$security->isLoggedIn()){
      $this->display_error(401, "Must Login to Build");
    }else{
      $user_id = $security->getUser()->getID();
    }


    // screen for bad data
    //$this->validate($data);
        
    //get query items
    $generator_id = $data->generator_id;
    $arguments    = json_encode($data->arguments);

    //generate a unique identifier
    $tempfile = tempnam("/data/storage","");
    unlink($tempfile);
    //mkdir($tempfile); // Was causing this directory to be created by Apache User
    $store_url = $tempfile;
    $parts = (split("/",$store_url));
    $identifier = $parts[count($parts) - 1];

    $status = "queued";
    
    // okay to create
    $query = "INSERT INTO product (user_id,generator_id,arguments,identifier,status) values (%d,%d,'%s','%s','%s');";
    $query = sprintf($query,$user_id,$generator_id,$arguments,$identifier,$status);
    db_query($query,true);

    //fetch ID 
    $product_id = mysql_insert_id();
    
    //return new object
    $query = "SELECT * FROM product WHERE id=%d";
    $query = sprintf($query,$product_id);
    $product = db_query($query,true);
    $product = $product[0]; // remove the list
    $this->display_result(201,$product);

  }
  
  private function update($data){
    
    //only allowed if logged in
    $security = Security::getInstance();    
    if(!$security->isLoggedIn()){
      $this->display_error(401);
    }


    $this->display_error(500, "Updates Forbidden");

  }


  private function validate($product){


    //ensure generator_id is present
    if(!array_key_exists("generator_id",$product)){
        $this->display_error(422, "generator_id is a required field");      
    }

   
    //ensure arguments are present
    if(!array_key_exists("arguments",$product)){	
        $this->display_error(422, "arguments is a required field");      
    }
        
    return true;
  }


  private function validate_arguments($generator_id, $arguments){

  }

}
?>
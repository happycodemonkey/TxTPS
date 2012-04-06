<?php
abstract class Handler {


  protected abstract function getTableName();
  
  protected function filter($row){
    return $row; //default is no modification
  }

  public function handle($request){
    switch($request->method){
      case "POST":
        $this->post($request);
      	break;
      case "GET":
        $this->get($request);
	break;
      case "HEAD":
      	$this->head($request);
        break;
      case "PUT":
      	$this->put($request);
	break;
      case "DELETE":
      	$this->delete($request);
	break;
      default:
      	$this->display_error(405); // method not allowed
    }
  }      

  protected function get($request){
     $tableName = $this->getTableName();


     // list of items
     if(strlen($request->id) == 0){
       $query     = sprintf("SELECT * FROM %s;", $tableName);
       $items = db_query($query,true);
     


      //filter
      for($i = 0; $i < count($items); $i++){
        $items[$i] = $this->filter($items[$i]);
      }

      //display    
      $this->display_result(200,$items);

    //search
    }elseif($request->id[0] == "?"){

     
      $encodedSearch = substr($request->id,1);
      $searchParts   = explode("&", $encodedSearch);
      $fields        = array();
      $query         = "";

      //decompose search into fields      
      foreach($searchParts as $sPart){
        if(strpos($sPart, "=",2) < 0){
          continue; // not a valid search field 
	}else{
	  $fParts = explode("=", $sPart,2);
	  if(count($fParts) < 2){
	    continue; //must have a key and value
	  }else{
	    $key   = $fParts[0];
	    $value = $fParts[1];

	    $key   = urldecode($key);
	    $value = urldecode($value);
	    

	    //validate key and value
	    $alphaPattern    = "/^[a-zA-Z]/";
	    $alphaNumPattern = "/^[-a-zA-Z0-9{}]/";

	    if(preg_match($alphaPattern,$key) == 0 ){
	      continue; //invalid chars in key
	    }

	    if( preg_match($alphaNumPattern,$value) == 0 ){
	      continue; //invalid chars in value
	    }

	    //store key and value
	    $fields[$key]= $value;
	  }
        }
      }

      if(count($fields) < 1){ //empty query
      	$query = sprintf("SELECT * FROM %s",$tableName);
      }else{	//real query
	$query = sprintf("SELECT * FROM %s WHERE ", $tableName);
	 
	$fieldCount = 0;
	foreach($fields as $key=>$value){
	    
	  //prefix with AND
	  if($fieldCount > 0){
	    $query .= " AND ";
	  }
	    
	  if(!is_numeric($value)){
	    $value = "'$value'";
	  }

	  $query = sprintf("%s %s=%s ", $query, $key,$value);
	  $fieldCount++;
        }
      }

      //search
      $items = db_query($query, true);

      //filter
      for($i = 0; $i < count($items); $i++){
        $items[$i] = $this->filter($items[$i]);
      }

      //return results
      $this->display_result(200,$items);

    // specific item
    }else{
      $query = sprintf("SELECT * FROM %s WHERE id=%d", $tableName, $request->id);
      $item  = db_query($query,true);

      if(count($item) < 1){
        $item = array();  //return the empty set
      }else{
        $item = $item[0]; //remove the outer array
      }

      $item = $this->filter($item);

      $this->display_result(200,$item);
    }
  }

  protected function post($request){	
    $this->display_error(501, "Post Method Not Implemented");
  }

  protected function head($request){
    $this->display_error(501, "Head Method Not Implemented");
  }

  protected function put($request){
    $this->display_error(501, "Put Method Not Implemented");
  }
 
  protected function delete($request){
    $this->display_error(501, "Delete Method Not Implemented");
  }



  protected function require_field($obj, $field){
  
    if(array_key_exists($field,get_object_vars($obj))){
       return true;
    }else{
      $this->display_error(422,"$field is required. You gave:<br>" . json_encode($obj));
    }   
  
  }



  protected function display_result($status_code, $php_obj){
    $json = json_encode($php_obj);
    header('X',true, $status_code);
    echo $json;
    	 
    exit(0);
  }

  protected function display_error($status_code, $err_msg = ""){
    //echo headers_sent();
    header($_SERVER["SERVER_PROTOCOL"]. " $status_code");

    //echo headers_sent();
   
    
      
    echo "<b>HTTP $status_code. Your request could not be completed.</b>";
    echo "<br>";
    echo "<b> $err_msg </b>";
    exit(0);
  }

}
?>

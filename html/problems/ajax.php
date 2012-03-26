<?php
require_once(dirname(__FILE__) . "/../includes/db/generators.inc.php");

$type = $_POST['type'];

if($type == "group"){
  $group_id   = $_POST['group_id'];
  $glist = generator_list("id",0,10000, $group_id);
  $generators = array();
  
  foreach($glist as $g){
    $id = $g['id'];
    $name = $g['name'];
    #$description = $g['description'];
    $obj = "{\"id\":\"$id\",\"name\":\"$name\",\"description\":\"\"}";
    $generators[] = $obj;
  }
  
  $json = "[" . implode(",", $generators) . "]";
  echo $json;




} elseif ($type == "generator"){
  $generator_id = $_POST['generator_id'];
  $arg_list = generator_arguments_get($generator_id);
  $details  = generator_details_get($generator_id);
  
  $args = array();
  
  $collection_id = $details['collection_id'];
  
  //serialize each object
  foreach($arg_list as $properties){
    $obj_str = "";
    $obj_arr = array();
    
    foreach($properties as $key => $value){
      if($key == "options"){
	$value = implode("\",\"",unserialize($value));
	$value = "[\"" . $value . "\"]";
	$pstring = "\"$key\":$value";
	$obj_arr[] = $pstring;
      } else {
	$pstring = "\"$key\":\"$value\"";
	$obj_arr[] = $pstring;
      }
    }

    $obj_str = implode(",", $obj_arr);
    $obj_str = "{" . $obj_str . "}";
    $args[] = $obj_str;
  }
  
  //combine the objects into an list
  $lstr = implode(",",$args);
  $lstr = "[" . $lstr . "]";

  $json_str = "{\"generator_id\":\"$generator_id\",\"collection_id\":\"$collection_id\", \"arguments\":$lstr}";
  
  echo $json_str;
  
  


} elseif ($type == "validation"){
  echo "NOT IMPLEMENTED";
} elseif ($type == "submit"){
  echo "NOT IMPLEMENTED";
} elseif ($type == "status"){
  echo "NOT IMPLEMENTED";
} else {
  echo "ERROR";
}

?>

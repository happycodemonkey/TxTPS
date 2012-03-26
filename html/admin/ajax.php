<?php

include_once("../includes/db/generators.inc.php");

$path   = "";
$type   = "";
$item   = "";
$method = $_SERVER['REQUEST_METHOD'];


//parse path
$path = $_GET['path'];
if($path[0] == '/'){
  $path = substr($path,1);
}


$parts = preg_split("/\//", $path);
$type = $parts[0];
if(count($parts) > 1){
  $item = $parts[1]; 
}



$generator = generator_details_get($item); 
$json      = json_encode($generator);

echo $json;



?>
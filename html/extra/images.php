<?php

require_once "../includes/db/common.inc.php";

$resource_type = $_GET['type'];
$resource_id   = $_GET['id'];
$file_name    = $_GET['name'];



//if this is a problem type, get the "correct" id instead of the identifier

$query  = "SELECT * FROM product WHERE identifier='$resource_id';";
$result = db_query($query,true);
if(count($result) < 1){
  echo "No such problem.";
  exit(1);
}else{
  $resource_id = $result[0]['id'];
}



$root = "/data/images/";
$path = $root . $resource_type .'s/'. $resource_id .'/'. $file_name;


//verify that file is in the database 
$query  = "SELECT * FROM image WHERE resource_type='%s' AND resource_id=%d AND name='%s'";
$query  = sprintf($query, $resource_type, $resource_id, $file_name);
$result = db_query($query, true);
if(count($result) < 1){
  echo "No record of that file.";
  exit(1);
}


//verify that file exists
if(!file_exists($path)){
  echo "File ($path) does not exist.";
  exit(1);
}


$target = $path;
$mime_type = mime_content_type($target);
  
$name_parts = explode("/", $target);
$download_name = $name_parts[count($name_parts) - 1];
$local_file = $target;
 
// send headers
header('Cache-control: private');
header('Content-Type: ' . $mime_type); 
header('Content-Length: '.filesize($local_file));
header('Content-Disposition: filename='.$download_name);
  
// flush content
flush();
  
// open file stream
$file = fopen($local_file, "rb");
  
// send the file to the browser
print fread ($file, filesize($local_file)); 
  
// close file stream
fclose($file);
?>
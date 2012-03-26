<?php

include_once("Request.php");
include_once("Handler.php");
include_once("ArgumentHandler.php");
include_once("StatisticHandler.php");
include_once("FileHandler.php");
include_once("ImageHandler.php");
include_once("ScriptHandler.php");
include_once("CollectionHandler.php");
include_once("GeneratorHandler.php");
include_once("UserHandler.php");
include_once("ProductHandler.php");


$path   = "";
$type   = "";
$item   = "";
$method = "";

//retrive data
if(php_sapi_name() == "cli"){
  $method = "GET";
  $path   = $argv[1];
}else{
  $method = $_SERVER['REQUEST_METHOD'];
  $path   = $_GET['path'];
}

//parse path
if($path[0] == '/'){
  $path = substr($path,1);
}
$parts = preg_split("/\//", $path);
$type = $parts[0];
if(count($parts) > 1){
  $item = $parts[1]; 
}

//build request
$r = new Request();
$r->method = $method;
$r->type   = $type;
$r->path   = $path;
$r->id     = $item;
$r->data   = "";
$r->hash   = "";


//select handler
$h = null;
switch($type){
  case "arguments":
    $h = new ArgumentHandler();
    break;
  case "statistics":
    $h = new StatisticHandler();
    break;
  case "files":
    $h = new FileHandler();
    break;
  case "images":
    $h = new ImageHandler();
    break;
  case "scripts":
    $h = new ScriptHandler();
    break;
  case "collections":
    $h = new CollectionHandler();
    break;
  case "generators":
    $h = new GeneratorHandler();
    break;
  case "products":
  case "problems":
    $h = new ProductHandler();
    break;
  case "users":
    $h = new UserHandler();
    break;
}

//handle
if($h != null){
  $h->handle($r);
  

  //if we get here something went wrong
  $h->display_error(500, "The Server Was Unable To Complete This Request");
}


?>
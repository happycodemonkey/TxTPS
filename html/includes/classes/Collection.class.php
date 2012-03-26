<?php
require_once(dirname(__FILE__) . "/../db/generators.inc.php");

abstract class Collection{
		
  //user should not be allowed to instatitate a collection. 
  protected function __construct(){}
	
  final public static function loadByID($collectionID){
		
    //check the singleton cache
    if(isset($collectionCache[$collectionID])){
      return $collectionCache[$collectionID];
    }
		
    //see if collection exists in database ; this will fail if the collection has not been installed 
    $details = generator_collection_get($collectionID);
    $classFile = $details ['class_file'];
    $className = basename($classFile);
    $className = substr($className, 0, strlen($className)-4);



    require_once($classFile);

		
    //throw error if doesn't not exist or invalid
    $collection = call_user_func(array($className,"getInstance"));
    return $collection;
  }
	
	
  final public function install(&$errorMsg, $classFile){
    //install collection
		
	
    $name = $this->getName();
    $desc = $this->getDescription();
    $collectionID = generator_collection_add($name, $desc, $classFile);
	
    foreach($this->getGenerators() as $generator){

      $gname = $generator->getName();
      $gdesc = $generator->getDescription();
      $gformat = $generator->getFormat();
			
      //TODO FIX FORMAT ID
      $generatorID = generator_add(get_class($generator),$gname, $gdesc, 0, $collectionID);
			
      generator_arguments_set($generatorID, $generator->getArgs());
    }	
    return true;
  }


  final public function reinstall($id, &$errorMsg){
    //reinstall collection

    $name = $this->getName();
    $desc = $this->getDescription();
		
    //update name and description for colleciton
    $collectionID = generator_collection_update($id, $name, $desc);
    
    $count = count($this->getGenerators());
    echo "<h2>Updating $name with $id</h2>";
    echo "<p>Collection has $count generators</p>";

    //update
    foreach($this->getGenerators() as $generator){
      var_dump($generator);

      $gname = $generator->getName();
      $gdesc = $generator->getDescription();
      $gformat = $generator->getFormat();

      echo "<h2>Installing $gname</h2>"; 
  			
      //update generator data
      $generatorID = generator_update(get_class($generator),$gname, $gdesc, 0, $id);
			
      //new generator 
      if($generatorID == false){
	$generatorID = generator_add(get_class($generator),$gname, $gdesc, 0, $id);
      }
			
      //overwrite the old values
      generator_arguments_set($generatorID, $generator->getArgs());
    }	
    return true;
  }


       
	
  public static  abstract function getInstance();
  public abstract function getName();
  public abstract function getDescription();
  public abstract function getGenerators();
  public abstract function getSize();
}
?>
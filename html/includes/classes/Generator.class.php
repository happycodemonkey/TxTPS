<?php 
abstract class Generator{

  final public static function loadByID($generatorID){

    $info = generator_details_get($generatorID);
    $collectionID = $info['collection_id'];
    $className        = $info['class'];
    $collection = Collection::loadByID($collectionID);



    foreach($collection->getGenerators() as $generator){

      if($generator instanceof $className AND $generator instanceof Generator){
	return $generator;
      }
    }
  }

  protected function __construct(){}



  public static abstract function getInstance();

  public abstract function getName();

  public abstract function getDescription();

  /**
                Returns the argument array formated as follows
                [0]=paramter
                [1]=title
                [2]=type (string)
                [3]=Description
                [4]=Optional?
                [5]=Select values if 2 is "select"
  **/
  public abstract function getArgs();

  /**
                Returns the output format as a string
  **/
  public abstract function getFormat();

  /**
                Validates the arguments for the generator. Returns true on success and false on failure. Error message is aso provided in case of failure.
  **/
  public abstract function validate($args, &$errorMsg);
  public abstract function build($args, $dest);
}
?>
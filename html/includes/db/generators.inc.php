<?php
/*
 File: generators.inc.php
 Author: James Kneeland
 Purpose: Generator/Collection database abstraction
 Note: This file includes [common/properties].inc.php . Make sure not to include them, or use include_once. 
*/
include_once("common.inc.php");

/*
 Returns an array of generator information for the specified generator or
 false if the generator is not in the database. 
*/
function generator_details_get($generator_id){
  //$id = db_rinse($generator_id);
  $query = sprintf("SELECT * FROM generator WHERE id = '%s'",
				$generator_id);
  $result =  db_query($query, true);

  if(count($result) != 0){
    return $result[0]; //should only be one, as generator_id is unique
  }else{
    return false;
  }
}

/**
 Returns the list of arguments that need to be entered to run this generator
 **/
function generator_arguments_get($generator_id){
  //$id = db_rinse($generator_id);
	$query = sprintf("SELECT * FROM arguments WHERE arguments.generator_id = '%s' ORDER BY arguments.sequence",
				$generator_id);
	$result =  db_query($query, true);
	return $result;
}

/*
function generator_arguments_set($generator_id, $arguments){
  //$id = db_rinse($generator_id);

  //drop the old args
  $query = "DELETE FROM arguments WHERE id= '$generator_id'";
  db_query($query);
  echo $query; 
 
  foreach($arguments as $arg){
		$query = sprintf("INSERT INTO arguments (generator_id, name, variable, type) VALUES('%s','%s','%s','%s')",
					$generator_id,
					$arg['name'],
					$arg['variable'],
					$arg['type']);
		db_query($query);
		echo $query . "\n";
  }
  return true;
  } */

function generator_arguments_set($generator_id, array $arguments){
  //$id = db_rinse($generator_id);

  $query = "DELETE FROM arguments WHERE generator_id= '$generator_id'";
  db_query($query);
  echo $query;
  echo "------------------------------------------------------------------------------------------------";
  foreach($arguments as $arg){
                //[0]=paramter 
                //[1]=title
		//[2]=type (string)
		//[3]=Description
		//[4]=Optional?
		//[5]=Select values if 2 is "select" 
		$param = $arg[0];
		$title = $arg[1];
		$type = strtoupper($arg[2]);
		$description = $arg[3];
		$optional = $arg[4];
		$values = ($type == "SELECT")? serialize($arg[5]):"";
		$query = sprintf("INSERT INTO arguments (generator_id, name, variable, type, description, optional, options) VALUES('%s','%s','%s','%s','%s','%d','%s')",
					$generator_id,
					$title,
					$param,
					$type,
					$description,
					$optional, 
					$values);
		db_query($query);
		echo $query . "\n";
  }
  return true;
  }



function generator_argument_update($arg_id, $arg_name, $arg_seq, $arg_var, $arg_type, $arg_desc, $arg_opts, $arg_def){
  $query = sprintf("UPDATE arguments SET name = '%s' , sequence='%s' ,variable = '%s' , type = '%s' , description = '%s', options = '%s', default_value='%s'  WHERE id='%d' ",
					$arg_name,
		                        $arg_seq,
					$arg_var,
					$arg_type,
					$arg_desc,
		   serialize(explode(",", $arg_opts)),
		                        $arg_def,
		                        $arg_id);
 
  db_query($query);
}


function generator_argument_add($gen_id, $arg_name, $arg_var, $arg_type, $arg_desc, $arg_opts, $arg_def){

  $query = sprintf("INSERT INTO arguments SET name = '%s' , variable = '%s' , type = '%s' , description = '%s', options = '%s', default_value='%s', generator_id='%d' ",
					$arg_name,
					$arg_var,
					$arg_type,
					$arg_desc,
		   serialize(explode(",", $arg_opts)),
		                        $arg_def,
		                        $gen_id);

  db_query($query);
}


function generator_argument_delete($arg_id){

  $query = sprintf("DELETE FROM arguments WHERE id='%d'", $arg_id);

  db_query($query);
}

function arguments_list(){
  $query = "SELECT * FROM arguments";
  return db_query($query,true);
}

/*
 Updates the generator information for the specified generator.
 Returns true on success, false on failure.

 If the generator_id does not exist, this operation will fail. Use generator_add to
 add a new user. 
*/
function generator_update($id,  $name , $desc){
 // $generator_id = db_rinse($generator_id);
  $name = db_rinse($name);
  $desc = db_rinse($desc);


  $query = sprintf("UPDATE generator SET name='%s', description='%s'  WHERE id = '%s'",
				$name,
		                $desc, 
		                $id );
    
  // echo $query;
  $resource = db_query($query);
  
  return;
}


/*
 Adds a new generator to the table. 
 Returns new generator_id on success, false on failure.

function generator_add($name , $script, $desc, $type_id, $collection_id){
  $generator_id = db_rinse($generator_id);
  //$name = db_rinse($name);
  //$desc = db_rinse($desc);
  $type_id = db_rinse($type_id);
  $collection_id = db_rinse($collection_id);
  $query = sprintf("INSERT INTO generator SET name='%s', script='%s', description='%s', generatortype_id='%s', collection_id='%s'",
				$name,
				$script,
				$desc,
				$type_id,
				$collection_id);
  $resource = db_query($query);
  return mysql_insert_id();
}
*/

function generator_add($class, $name, $desc, $outputFormatID, $collectionID){
  $generator_id = db_rinse($generator_id);
  //$name = db_rinse($name);
  //$desc = db_rinse($desc);
  $type_id = db_rinse($type_id);
  $collectionID = db_rinse($collectionID);
  $query = sprintf("INSERT INTO generator (class, name, description, generatortype_id, collection_id) values('%s', '%s','%s','%s','%s')",
				$class,
				$name,
				$desc,
				$outputFormatID,
				$collectionID);
  $resource = db_query($query);
  return mysql_insert_id();
}



/*
 Removes the generator from the database. 

 Returns true if the generator is no longer in the database.
*/
function generator_delete($generator_id){

  $id = db_rinse($generator_id);
  //delete information from argument table
  $query = sprintf("DELETE FROM arguments WHERE arguments.generator_id='%s'",$id);
  echo $query;
  $resource = db_query($query);
  
  $query = sprintf("DELETE FROM generator WHERE generator.id='%s'",$id);
  echo $query;
  $resource = db_query($query);

  //check if the row was updated 
  $gen = generator_details_get($user_id);
  if($gen == false){
    return true;
  }else{
    return false;
  }
}


/*
 Returns an ordered list of generators. 
 $order_by : column by which to order the list
 $start : Initial offset
 $limit : Total number of results to return. 
 $cols : Specific columns to return. By default everything but password is returned. 
*/
function generator_list($order_by = "id", $start = 0, $limit = 20000000000, $collection_id = null){
  $where_clause = ""; 
  if($collection_id != null){
     $where_clause = sprintf(" WHERE (collection.collection_id = '%s') ",$collection_id);
  }

  $query = sprintf("SELECT generator.id as id, generator.description as description, generator.name as name, generatortype.name as type, collection.name as collection, collectiontype.name as format FROM generator LEFT JOIN collection ON generator.collection_id = collection.collection_id LEFT JOIN generatortype ON generator.generatortype_id = generatortype.id LEFT JOIN collectiontype ON collection.collectiontype_id = collectiontype.id %s ORDER BY %s LIMIT %s, %s",
				$where_clause,
				$order_by,
				$start,
				$limit);
  $result =  db_query($query, true);

  if(count($result) != 0){
    return $result; 
  }else{
    return false;
  }
}

/*
 Returns a list of generator collections order alpha aesc.
*/
function generator_collection_list(){
  //NOTE: collection.id has somehow been changed to collection.collection_id 
  //This change needs to be updated
  $query = "SELECT collection.collection_id as id, collection.name as name, collection.description as description,  count(generator.id) as count , collectiontype.name as format FROM collection LEFT JOIN generator ON collection.collection_id=generator.collection_id LEFT JOIN collectiontype ON collection.collectiontype_id=collectiontype.id GROUP BY collection.collection_id ,collection.name,collection.description;";
  $result =  db_query($query, true);

  if(count($result) != 0){
    return $result; 
  }else{
    return false;
  }
}
/*
 Adds a new collection. Returns collection_id on success
 or false on return.
*/
/* function generator_collection_add($name, $identifier ,$desc, $format){
	//$name = db_rinse($name);
	//$desc = db_rinse($desc);
	$query = sprintf("INSERT INTO collection (name, identifier, description, collectiontype_id) SELECT '%s','%s','%s', collectiontype.id as format FROM collectiontype WHERE collectiontype.name='%s' LIMIT 1;",
				$name,
				$identifier,
				$desc,
				$format);
	echo $query;

	$resource = db_query($query);
	//NOTE : Mysql affected rows does not work
	$link = db_get_link();
	return mysql_insert_id($link);
} */

function generator_collection_add($name, $desc, $classFile){
	//$name = db_rinse($name);
	$desc = db_rinse($desc);
	$query = sprintf("INSERT INTO collection (name, description, class_file) values('%s','%s','%s')",
				$name,
			        $desc,
				$classFile);

	$resource = db_query($query);
	//NOTE : Mysql affected rows does not work
	$link = db_get_link();
	return mysql_insert_id($link);
}



function generator_collection_update($id, $name, $desc){
	//$name = db_rinse($name);
	$desc = db_rinse($desc);
	$query = sprintf("UPDATE collection set name = '%s', description = '%s'  WHERE collection_id='%s'",
				$name,
 			        $desc,
			        $id);

	//echo $query;
	$resource = db_query($query);
	//NOTE : Mysql affected rows does not work
	$link = db_get_link();
	return mysql_insert_id($link);
}





/*
 Updates an existing collection. 

function generator_collection_update($collection_id, $name, $desc, $format_id){
	//$name = db_rinse($name);
	//$desc = db_rinse($desc);
	echo "Update";
	$query = sprintf("UPDATE collection set name='%s', description ='%s', collectiontype_id='%s' WHERE collection.collection_id = '%s'",
				$name,
				$desc,
				$format_id,
				$collection_id);
	echo $query;
	$resource = db_query($query);
	//NOTE : Mysql affected rows does not work
	$link = db_get_link();
	return mysql_insert_id($link);
}

*/

function generator_collection_get($collection_id){
  $id = db_rinse($collection_id);
  $query = sprintf("SELECT * FROM collection WHERE collection.collection_id=%s",$id);
  $result = db_query($query, true);

  //check if the class was deleted 
  if(count($result) != 0){
    return $result[0];
  }else{
    return false;
  }
}

function generator_collection_delete($collection_id){
  $id = db_rinse($collection_id);
  
  //delete all the generators in the collection first 
  $query = sprintf("SELECT generator.id FROM generator WHERE generator.collection_id='%s'",$id);
  $result = db_query($query, true);
  foreach($result as $row){
	var_dump($row);
	generator_delete($row['id']);
	echo $row['id'];
  }
  
  
  //then delete the collection
  $query = sprintf("DELETE FROM collection WHERE collection.collection_id='%s'",$id);
  $resource = db_query($query);

  return true;
}

?>

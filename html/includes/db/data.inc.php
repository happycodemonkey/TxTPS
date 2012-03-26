<?php
/*
 File: data.inc.php
 Author: James Kneeland
 Purpose: Generator/Collection database abstraction
 Note: This file includes [common/properties].inc.php . Make sure not to include them, or use include_once. 
*/
include_once("common.inc.php");


/*
 Returns an ordered list of products.
  $start : Initial offset
 $limit : Total number of results to return. 
 */
function product_list($col = 'id', $start = 0, $limit = 20){
  $query = sprintf(" SELECT product.id, product.identifier, product.created, product.hold, product.error, generator.name as gen_name, generator.id as generator_id, collection.name as col_name,  collection.id as collection_id FROM product LEFT JOIN generator ON product.generator_id = generator.id  LEFT JOIN collection ON generator.collection_id = collection.id ORDER BY %s LIMIT %s, %s",
				"product.". $col,
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
 Returns an ordered list of products.
  $start : Initial offset
 $limit : Total number of results to return. 
 */
function product_list_by_collection($col = 'id', $start = 0, $limit = 20, $collection_id){
  $query = sprintf("SELECT product.id, product.identifier, product.created, generator.name as gen_name, generator.id as generator_id, collection.name as col_name,  collection.id as collection_id FROM product LEFT JOIN generator ON product.generator_id = generator.id  LEFT JOIN collection ON generator.collection_id = collection.id WHERE collection.id = '%s' AND product.error <> 1 ORDER BY %s LIMIT %s, %s",
				$collection_id,
				"product.". $col,
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
 Returns an ordered list of products.
  $start : Initial offset
 $limit : Total number of results to return. 
 */
function product_list_by_generator($col = 'id', $start = 0, $limit = 20, $generator_id){
  $query = sprintf("SELECT product.id, product.identifier, product.created, generator.name as gen_name, generator.id as generator_id, collection.name as col_name,  collection.collection_id as collection_id FROM product LEFT JOIN generator ON product.generator_id = generator.id  LEFT JOIN collection ON generator.collection_id = collection.collection_id WHERE generator_id = '%s' AND (product.error = 0 OR product.error IS NULL) ORDER BY %s LIMIT %s, %s",
				$generator_id,
				"product.". $col,
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
 Returns an ordered list of products.
  $start : Initial offset
 $limit : Total number of results to return. 
 */
function product_list_by_user($col = 'id', $start = 0, $limit = 20, $user_id){
  $query = sprintf(" SELECT product.id, product.identifier, product.created, generator.name as gen_name, collection.name as col_name FROM product LEFT JOIN generator ON product.generator_id = generator.id  LEFT JOIN collection ON generator.collection_id = collection.collection_id WHERE product.user_id = %s ORDER BY %s LIMIT %s, %s",
				$user_id,
				"product.". $col,
				$start,
				$limit);
  $result =  db_query($query, true);

  if(count($result) != 0){
    return $result; 
  }else{
    return false;
  }
}




/**
Inserts a request into the database for generation using the arguments passed
**/
function product_generate($generator_id, $user_id, $arguments, $description){



  $desc = db_rinse(htmlentities($description));
  $store_url = tmpdir("/data/storage/", "");
  $parts = (split("/",$store_url));
  $identifier = $parts[count($parts) - 1];

  shell_exec("chmod 777 $store_url");



  $query = sprintf("INSERT INTO product (generator_id,user_id,producttype_id,arguments,description,identifier) VALUES('%s','%s','%s','%s','%s','%s')",
		   $generator_id,
		   $user_id,
		   $product_id,
		   serialize($arguments),
		   $desc,
		   $identifier);
  $result =  db_query($query, true);

	if(count($result) != 0){
	    return $result[0]; //should only be one, as product_id is unique
	}else{
	    return false;
	}
}

/**
Called by the generator routine to update the product's information in the database after generation.

This function updates the timestamp so that the product will not be regenerated. 
**/
function product_store($product_id, $identifier){
	$query = sprintf("UPDATE product set product.identifier='%s', product.created=NOW() WHERE id = '%s'",
					$identifier,
					$product_id);
	$result =  db_query($query);

	if(count($result) != 0){
	    return $result[0]; //should only be one, as product_id is unique
	}else{
	    return false;
	}
}

/**
Returns information including the identifier of the product specified
**/
function product_get($identifier){
	$query = sprintf("SELECT *, product.id, product.identifier, product.created, product.description, generator.description as  gen_desc, collection.description as col_desc, generator.name as gen_name, collection.name as col_name FROM product LEFT JOIN generator ON product.generator_id = generator.id  LEFT JOIN collection ON generator.collection_id = collection.id WHERE product.identifier='%s'",
					$identifier);
	$result =  db_query($query, true);

	if(count($result) != 0){
	    return $result[0]; //should only be one, as product_id is unique
	}else{
	    return false;
	}
}

/**
Returns information including the identifier of the product specified
**/
function product_get_by_id($id){
	$query = sprintf("SELECT *, product.id, product.identifier, product.created, generator.description as  gen_desc, collection.description as col_desc, generator.name as gen_name, collection.name as col_name FROM product LEFT JOIN generator ON product.generator_id = generator.id  LEFT JOIN collection ON generator.collection_id = collection.collection_id WHERE product.id='%s'",
					$id);
	$result =  db_query($query, true);

	if(count($result) != 0){
	    return $result[0]; //should only be one, as product_id is unique
	}else{
	    return false;
	}
}

function product_queue(){
	//$query = sprintf("SELECT * from product  WHERE product.created IS NULL AND (product.error IS NULL OR product.error = 0) AND (product.hold IS NULL OR product.hold =0)");
	$query = "SELECT * FROM product WHERE product.status = 'queued'";
	$result =  db_query($query, true);
	return $result;
}


function product_error($id){
  $query = "UPDATE product SET product.error = 1, product.status='error' WHERE product.id = $id";
  $result = db_query($query);
}


function product_hold($id){
  $query = "UPDATE product SET product.hold = 1 WHERE product.id = $id";
  $result = db_query($query);
}


function product_rebuild($id){

  //files
  $query = "delete from file WHERE product_id = $id";
  $result = db_query($query);

  //stats
  $query = "delete from anamod_data WHERE product_id = $id";
  $result = db_query($query);

  //images
  $query = "delete from images WHERE product_id = $id";
  $result = db_query($query);

  //clear hold and error
  $query = "update product set product.created=NULL, product.error=0, product.hold=0 where product.id = $id";
  $result = db_query($query);
}


function product_delete($id){

  //product table
  $query = "delete from product WHERE id = $id";
  $result = db_query($query);
  
  //files
  $query = "delete from file WHERE product_id = $id";
  $result = db_query($query);

  //stats
  $query = "delete from anamod_data WHERE product_id = $id";
  $result = db_query($query);

  //images
  $query = "delete from images WHERE product_id = $id";
  $result = db_query($query);
}

function product_unhold($id){
  $query = "UPDATE product SET product.hold = 0 WHERE product.id = $id";
  $result = db_query($query);
}


function product_file_attach($productId,$fileName,$fileSize){

  //determine file type
  $parts = split('.' , $fileName);
  $fileType;
  $ext;

  //no extension
  if($parts < 2){
    $fileType = 1; //default unknown type
  }else{
    $ext = $parts[count($parts) -1];//last part is ext
    
    //query 
    $ftQuery = "SELECT id FROM filetype WHERE ext='$ext'";
    $rows = db_query($ftQuery, true);

    if(count($rows) < 1){ //no filetype known
      $fileType = 1;//unknown
    }else{
      $fileType = $rows[0]['id'];
    }

  }



  $query = "INSERT INTO file (product_id, name, size, filetype_id) VALUES ('$productId', '$fileName','$fileSize', '$fileType')";

  
  $result = db_query($query);
}

function product_file_list($productID){
  $query = "SELECT file.*, filetype.name as type FROM file  LEFT JOIN filetype ON filetype.id=file.filetype_id WHERE product_id = '$productID' ORDER BY file.name";
  return db_query($query, true);
}

function product_setcmd($identifier, $cmd){
  $query = "UPDATE product SET cmd ='$cmd' WHERE identifier = '$identifier'";
  return db_query($query, true);
}



function file_info($fileID){
  $query = "SELECT file.*,product.* FROM file LEFT JOIN product ON product.id = file.product_id WHERE file.id = '$fileID'";
  
  // echo $query;
  $result = db_query($query, true);
  
  //var_dump($result);

  if(count($result) > 0){
    return $result[0];
  }else{
    return false;
  }
}

function product_count(){
	$query = sprintf("SELECT count(id) as count FROM product");
	$result =  db_query($query, true);
	return $result[0]['count'];
}

function statistics_queue(){
  $query = "SELECT file.*, filetype.name as type, product.identifier as identifier from file LEFT JOIN product ON file.product_id = product.id LEFT JOIN anamod_data ON file.id = anamod_data.file_id LEFT JOIN filetype ON filetype.id=file.filetype_id WHERE anamod_data.file_id IS NULL";
  
    $result = db_query($query, true);
  return $result;
  
}


function image_queue(){
  $query = "SELECT file.name, image.*, product.identifier LEFT JOIN image ON file.id = image.file_id LEFT JOIN product ON product.id = file.product_id WHERE image.file_id IS NULL";
  

  $result = db_query($query, true);
  return $result;
  
}




function tmpdir($path, $prefix)
{
  // Use PHP's tmpfile function to create a temporary
  // directory name. Delete the file and keep the name.
  $tempname = tempnam($path,$prefix);
  if (!$tempname)
    return false;

  if (!unlink($tempname))
    return false;

  // Create the temporary directory and returns its name.
  if (mkdir($tempname))
    return $tempname;

  return false;
}


?>

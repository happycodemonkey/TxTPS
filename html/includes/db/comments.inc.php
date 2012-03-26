<?php
/*
 File: comments.inc.php
 Author: James Kneeland
 Purpose: Interface to comment database 
 Note: This file includes [common/properties].inc.php . Make sure not to include them, or use include_once. 
*/
include_once("common.inc.php");



/*
 Returns an ordered list of products.
  $start : Initial offset
 $limit : Total number of results to return. 
 */

function comments_collection_list($id){
  return comments_list('collection', $id);
}
function comments_generator_list($id){
  return comments_list('generator', $id);
}
function comments_product_list($id){
  return comments_list('product', $id);
}




function comments_collection_add($id, $user, $message){
  return comments_add('collection', $id, $user, $message);
}

function comments_generator_add($id, $user, $message){
  return comments_add('generator', $id, $user, $message);
}

function comments_product_add($id, $user, $message){
  return comments_add('product', $id, $user, $message);
}

function comments_list($type, $id){
  $query = sprintf("SELECT CONCAT(user.firstname, ' ', user.lastname) as name, user.email as email, comment.time as timestamp, comment.message as message FROM comment LEFT JOIN user on user.id = comment.user_id WHERE comment.type='%s' AND comment.id='%s'",
		   $type,$id);

 $result =  db_query($query, true);
 return $result; 
}



function comments_add($type, $id, $user, $message){
  $query = sprintf("INSERT INTO comment (type, id, user_id, message, time)".
		   " values('%s','%s','%s','%s','%s');",
		   $type,$id,$user,$message, time());

  $result =  db_query($query, true);
  return $result; 
}

?>
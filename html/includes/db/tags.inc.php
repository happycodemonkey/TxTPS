<?php
/*
 File: tags.inc.php
 Author: James Kneeland
 Purpose: Interface to tag database 
 Note: This file includes [common/properties].inc.php . Make sure not to include them, or use include_once. 
*/

require_once("common.inc.php");
require_once("generators.inc.php");


function tags_collection_list($id){
  return tags_list('collection', $id);
}
function tags_generator_list($id){
  return tags_list('generator', $id);
}
function tags_product_list($id){
  return tags_list('product', $id);
}


function tags_collection_search($tag){
  return tags_search('collection', $tag);
}
function tags_generator_search($tag){
  return tags_search('generator', $tag);
}
function tags_product_search($tag){
  return tags_search('product', $tag);
}





function tags_collection_add($id, $tag){
  return tags_add('collection', $id, $tag);
}

function tags_generator_add($id, $tag){
  return tags_add('generator', $id, $tag);
}

function tags_product_add($id, $tag){
  return tags_add('product', $id, $tag);
}


//----------------------------------------------------------------------------//

function tags_list($type, $id){
 $query = sprintf("SELECT tagname.name as name FROM tag".
		   " LEFT JOIN tagname on tagname.id = tag.tagname_id WHERE ".
		   "tag.type='%s' AND tag.id=%s;",
		   $type,$id);
  $result =  db_query($query, true);
  return $result; 
}



function tags_add($type, $id, $tag){


  $tag = strtolower(trim($tag));  //enforce lowercase tags

  //tags can't be empty
  if(strlen($tag) < 1){
    return;
  }

  $query = sprintf("SELECT id FROM tagname WHERE name='%s'", $tag);
  $result =  db_query($query, true);
 
  //does tagname exist
  if(count($result) < 1)
  {
    $query = sprintf("INSERT INTO tagname (name) values('%s')", $tag);
    $result =  db_query($query, true); 
    
    $query = sprintf("SELECT id FROM tagname WHERE name='%s'", $tag);
    $result =  db_query($query, true);
  }
  
  $tagname_id = $result[0]['id'];



  //see if this item is already tagged  
  $query = "SELECT * FROM tag WHERE type='$type' AND tagname_id = '$tagname_id' AND id = '$id'";
		  

  $result =  db_query($query, true);
  if(count($result) < 1)
  {
    $query = sprintf("INSERT INTO tag (type, id, tagname_id)".
		     " values('%s','%s','%s');",
		     $type,$id,$tagname_id);
    $result =  db_query($query, true);
  }else
  {

    //do nothing

  }
  return $result; 
}



function tags_search($type, $tag){

  //enforce lowercase tags
  $tag = strtolower($tag);

  $query = "SELECT id FROM tagname WHERE name='" . $tag . "'";


 
  $result =  db_query($query, true);
 
  //does tagname exist
  if(count($result) < 1)
  {

    //this tag doesn't exist, return empty set

    return array();
  }

  $tagname_id = $result[0]['id'];

  $table = $type;
  $pk    = "id";

  if($type == "collection"){

    $pk = "collection_id";
  }


  $query = "SELECT tag.id as id, $table.name as name FROM tag LEFT JOIN $table on $table.$pk = tag.id WHERE type='$type' AND tagname_id = '$tagname_id'";



  $result_set = db_query($query,true);




  return $result_set;
}


function tags_rebuild()
{
  //clear old tags
  $query = "DELETE FROM tag WHERE 1=1";
  db_query($query);
  $query = "DELETE FROM tagname WHERE 1=1";
  db_query($query);


  //parse the generators and collections and add the new tags
  $generators = generator_list();
  $collections = generator_collection_list();


  foreach($generators as $generator){
   
    $desc = $generator['description'];
    $name = $generator['name'];
    $id   = $generator['id'];
    $tags = array_merge(explode(" ", $desc) , explode(" ", $name));
    foreach($tags as $tag){
      //echo $tag;
     tags_generator_add($id,$tag);
    }
  }


  foreach($collections as $collection){
    
    $desc = $collection['description'];
    $name = $collection['name'];
    $id   = $collection['id'];
    $tags = array_merge(explode(" ", $desc),explode(" ", $name));
    foreach($tags as $tag){
      //echo $tag;
      tags_collection_add($id,$tag);
    }
  }

}



?>
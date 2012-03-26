<?php
/*
 File: common.inc.php
 Author: James Kneeland
 Purpose: Core database abstraction
 Note: This file includes properties.inc.php . Make sure not to include it, or use include_once. 
*/
include_once("properties.inc.php");

$link = null;

/*
 Returns link or null if not connected.
*/
function db_get_link(){
  global $link;
  return $link;
}

/*
 Returns whether or not the connection is active/valid
*/
function db_is_connected(){
  global $link;
  return (isset($link) && $link != null);
}

/*
 Establishes a connection to the database per settings. If a connection already exists, it is closed.
*/
function db_connect(){
  global $link;
  $link = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
  mysql_select_db(DB_DATABASE, $link);
}

/*
 Disconnects the current connection, if it is valid. 
*/
function db_disconnect(){
  global $link;
  if(db_is_connected()){
    return mysql_close($link);
  }else{
    return false;
  }
}

/*
 Runs a generic query on the database per mysql_query.  If $return_rows is set to true, then the
 resource is not returned but instead the fetched rows (using assoc/column headers) are returned. 
*/
function db_query($string, $return_rows= false){
  
  if(!db_is_connected()){
    db_connect();
  }

  $result = mysql_query($string, db_get_link());
  if(!$return_rows){
    return $result;
  }else{
    $result_set = array();
    $i = 0;
    while ($row = mysql_fetch_assoc($result)) {
        foreach ($row as $key => $value) {
            $result_set[$i][$key] = $value;
        }
        $i++;//next row
    }
    return $result_set;
  }
}

/* 
 Returns the last error/error number from the connection.
*/
function db_error(){
  global $link;
 $link = db_get_link();
 if(db_is_connected()){
   return mysql_errno($link) . ": " . mysql_error($link);
 }else{ 
  return null;
 }
}

/*
 Cleans potentially hazardous input.
*/
function db_rinse($data){
  global $link;
  db_connect();

 return (mysql_real_escape_string($data, $link));
}

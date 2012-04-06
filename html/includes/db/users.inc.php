<?php
/*
 File: users.inc.php
 Author: James Kneeland
 Purpose: User information database abstraction
 Note: This file includes [common/properties].inc.php . Make sure not to include them, or use include_once. 
*/
include_once(getEnv("DOCUMENT_ROOT") . "/includes/db/common.inc.php");

/*
 Returns an array of user information for the specified user or
 false if the user is not in the database. 

 Note: If by_id is true, the $identity is assumed to be a email rather
 than the user_id. Otherwise, $identity is assumed to be a user_id.
*/
function user_details_get($identity, $by_email = false){
	//$identity = db_rinse($identity);
	$type = (($by_email)?"email":"id");
	$query = sprintf("SELECT id, firstname, lastname, email, country, affiliation, userclass_id FROM user WHERE user.%s = '%s'",
				$type,
				$identity);
	$result =  db_query($query, true);

	if(count($result) != 0){
		return $result[0]; //should only be one, as username/user_id are unique
	}else{
		return false;
	}
}

/*
 Updates the user information for the specified user. Password
 changes must occur via user_update_password. Returns true on success,
 false on failure.

 If the user_id does not exist, this operation will fail. Use user_add to
 add a new user. 
*/
function user_details_update($user_id, $firstname, $lastname, $class_id, $email, $country, $affiliation, $noNotifications){
  // $user_id = db_rinse($user_id);
  // $name = db_rinse($name);
  // $class_id = db_rinse($class_id);
  // $email = db_rinse($email);
	$query = sprintf("UPDATE user SET firstname='%s', lastname = '%s', email='%s',country='%s', affiliation='%s', userclass_id='%s', no_notifications='%s' WHERE (user.id = 
'%s')",
				$firstname,
				$lastname,
				$email,
				$country,
				$affiliation,
				$class_id,
			        $user_id,
			        $noNotifications
                                );
	$resource = db_query($query);
  //check if the row was updated 
  if(mysql_affected_rows($resource) != 0){
    return true;
  }else{
    return false;
  }
}

/*
 Updates the user's password in the database. $password
 is plaintext, but is hashed. Returns true on success, false on failure.
*/
function user_password_update($user_id, $password){
  $info = user_details_get($user_id);
  $hash = user_password_hash($info['email'], $password);
  $query = sprintf("UPDATE user SET password_hash='%s' WHERE (id = '%s')",
				$hash,
				$user_id);
  $resource = db_query($query);

  //check if the row was updated 
  if(mysql_affected_rows($resource) != 0){
    return true;
  }else{
    return false;
  }
}







/*
Activates the user's account
*/
function user_activate($activation_key){
  $count = 0;
  $query = sprintf("SELECT * FROM user where activated=false AND activation_key = '%s' ",
				$activation_key);
  $result = db_query($query, true);
  $count = count($result);
  //check if the row was updated 
  if($count > 0){
    $query = sprintf("UPDATE user SET activated=true WHERE (activation_key = '%s')",
				$activation_key);
    $resource = db_query($query);
    return true;
  }else{
    return false;
  }
}

function user_activate_get_key($user_id){
  $query = sprintf("SELECT activation_key from user WHERE id = '%s'",
				$user_id);
  $result = db_query($query, true);

  //check if the row was updated 
  if(count($result) > 0){
    return $result[0]['activation_key'];
  }else{
    return false;
  }
}
/*
 Hashes the provided password using a common hashing algorithm. 
*/
function user_password_hash($email, $password){
  //hash function goes here
  return sha1($password . $email);
}

/*
 Adds a new user to the table. 
 Username must be unique and password is plaintext.
 
 Returns new user_id on success, false on failure.
*/
function user_add($firstname, $lastname, $class, $email, $country, $affiliation, $password){

	$activation_key = sha1($email . time());
	
	$query = sprintf("SELECT * FROM user where email='%s' ",
				$email);
	$result = db_query($query, true);
	//do not allow adds if the user already exists in the database.
	if(count($result) > 0){
		return false;
	}
	
	//@TODO: THIS IS A HACK to ensure that people can log in as soon as they register since it doesn't appear that activation
	// functionality has been implemented yet. 
	$query = sprintf("INSERT INTO user (firstname,lastname,email,country,affiliation,userclass_id,password_hash, activation_key, activated) 
VALUES('%s', '%s', '%s','%s','%s','%s','%s','%s','1')",
					$firstname,
					$lastname,
					$email,
					$country,
					$affiliation,
					$class,
					user_password_hash($email, $password),
					$activation_key);
	//echo $query;
    $resource = db_query($query);


  //check if the row was updated 
  //$user = user_details_get($name, true);
  //if(isset ($user[0]['id'])){
  if(true){
    return mysql_insert_id();
  }else{
    return false;
  }
}

/*
 Removes the user from the database. If $delete_data is
 set to true, then all the user's generated data is removed too.
 If not, it is unassigned from the user and orphaned. 

 Returns true if the user is no longer in the database.
*/
function user_delete($user_id, $delete_data = false){
  //TODO : Actually obey $delete_data 

  $id = db_rinse($user_id);
  $query = sprintf("DELETE FROM user WHERE user.id='%s'",$id);
  $resource = db_query($query);

  //check if the row was updated 
  $user = user_details_get($user_id);
  if($user == false){
    return true;
  }else{
    return false;
  }
}


/*
 Returns an ordered list of users. 
 $order_by : column by which to order the list
 $start : Initial offset
 $limit : Total number of results to return. 
 $cols : Specific columns to return. By default everything but password is returned. 
*/
function user_list($order_by = "id", $start = 0, $limit = 20){
  $query = sprintf("SELECT user.id as id, concat(user.lastname, ' \, ', user.firstname) as name, user.lastname, user.firstname, user.email as email, userclass.name as class FROM user LEFT JOIN userclass ON user.userclass_id=userclass.id ORDER BY %s LIMIT %s, %s",
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
 Returns a list of user classes order alpha aesc.
*/
function user_class_list(){
  $query = "SELECT * FROM userclass ORDER BY 'name' ASC";
  $result =  db_query($query, true);

  if(count($result) != 0){
    return $result; 
  }else{
    return false;
  }
}

function user_class_add($name, $desc){
  $name = db_rinse($name);
  $desc = db_rinse($desc);
  $query = sprintf("INSERT INTO userclass (name,description) VALUES('%','%')",
				$name,
				$desc);
  $resource = db_query($query);

  //check if the class was added 
  if(mysql_rows_affected($resource) != 0){
    return true;
  }else{
    return false;
  }
}

function user_class_delete($class_id){
  $id = db_rinse($class_id);
  $query = sprintf("DELETE FROM userclass WHERE id='%'",$id);
  $resource = db_query($query);

  //check if the class was deleted 
  if(mysql_rows_affected($resource) != 0){
    return true;
  }else{
    return false;
  }
}

?>

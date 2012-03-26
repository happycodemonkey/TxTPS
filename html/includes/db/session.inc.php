<?php
/*
 File: session.inc.php
 Author: James Kneeland
 Purpose: Session/Security database abstraction
 Note: This file includes [common/properties].inc.php . Make sure not to include them, or use include_once. 
*/
require_once(dirname(__FILE__) . "/common.inc.php");
require_once(dirname(__FILE__) . "/users.inc.php");

/*
Returns a user id and writes into the session table if the login is correct or false on failure. 
*/
function session_login($email, $password, $session_id, $ip){
	$pwd_hash = user_password_hash($email, $password);
	//check if the user has the correct login
	$query = sprintf("SELECT id FROM user WHERE email = '%s' AND password_hash = '%s' AND activated = true LIMIT 1",
				$email,
				$pwd_hash);
	$result =  db_query($query, true);

	//if correct, insert into session table
	if(count($result) != 0){
	    $user_id = $result[0]['id'];
		$query = sprintf("INSERT INTO session (session_id, user_id, last_access, ip_addr) VALUES ('%s', '%s', '%s', '%s') ON DUPLICATE KEY UPDATE last_access = '%s'",
					$session_id,
					$user_id,
					time(),
					$ip,
					time());
		$result =  db_query($query, true);
		return true;
	}else{
		//bad user name or password
		return false;
	}
}

function session_logout($session_id){
  //$id = db_rinse($generator_id);
	$query = sprintf("DELETE FROM session WHERE session_id = '%s'",
				$session_id);
	$result =  db_query($query, true);
}

function session_info($session_id){

	$query = sprintf("SELECT session.user_id as user_id , concat(user.firstname,' ', user.lastname) as name, user.email as email, userclass.id as userclass_id, userclass.name as userclass_name FROM session LEFT JOIN user ON session.user_id = user.id LEFT JOIN userclass ON user.userclass_id = userclass.id WHERE session.session_id = '%s'",
				$session_id);
	$result =  db_query($query, true);
	return $result[0];
}

function session_is_valid($session_id){
	$query = sprintf("SELECT * FROM session WHERE session_id='%s'",
				$session_id);
	$result =  db_query($query, true);
	return (count($result) > 0);
}
?>
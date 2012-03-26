<?php
/*
 File:  Security.class.php
 Author: James Kneeland
 Purpose: User information 
 Note: 
*/
require_once(dirname(__FILE__) . "/../db/session.inc.php");
require_once(dirname(__FILE__) . "/User.class.php");

class Security{
	//state vars
	private $loggedIn = false;
	private $sessionID = null;

	//cached and generated user information
	private $ip;
	private $referer;
	private $origin;
	private $user;
	
	//user permission level
	private $classID;
	private $className;
	
	//singleton 
	private static $instance;
	
	//to be called once!!
	private function __construct(){
	
		//initialize session
		session_save_path("/tmp/sessions");
		session_start(); 
		$this->sessionID = session_id();
		
		//change this to actually validate 
		$this->loggedIn = session_is_valid($this->sessionID);
		
		//HTTP vars
		$this->referer = $_SERVER['HTTP_REFERER'];
		$this->ip = $_SERVER['REMOTE_ADDR'];
		

		$this->origin = $this->referer; 
		
		// Information from database if user is logged in
		if($this->loggedIn){
			$this->update();
		}
	}
	
	//loads user information from database
	private function update(){
		$info = session_info($this->sessionID);
		$uid = $info['user_id'];
		$this->user = User::loadByID($uid);
	}
	
	
	//returns whether the user is logged in and that login is valid
	public function isLoggedIn(){
		return $this->loggedIn;
	}
	
	//user access methods
	public function login($email, $password){

		
		//This is the original referer to the site
	        if(!isset($this->origin)){
			$_SESSION['origin'] = $this->referer;
			
		}else{
        		$this->origin = $_SESSION['origin'];
		}
		  

		$result = session_login($email, $password, $this->sessionID, $this->ip);
		if($result == true){
			$this->loggedIn = true;
			$this->update();
		}
		return $result;
	}
	
	public function logout(){
		$result = session_logout($this->sessionID);
		$this->loggedIn = false;
		$this->user = null;
		session_destroy();
		return true;
	}
	
	//accesssor methods
	public function getUser(){
		return $this->user;
	}

	public function getReferer(){
		return $this->referer;
	}

	public function getOrigin(){
	        return $this->origin;
	}

	public function getIP(){
		return $this->ip;
	}

	public function getClassName(){
	  return $this->getUser()->getClassName();
	}

	public function getClassID(){
	  return $this->getUser()->getClassID();
	}
     
	
	//utility methods
	public static function forward($url){
		header("Location: $url");
	}
	
	public static function generatePassword($length){
		// start with a blank password
		$password = "";
		
		// define possible characters
		$possible = "0123456789bcdfghjkmnpqrstvwxyz"; 
		
		// set up a counter
		$i = 0;
		
		// add random characters to $password until $length is reached
		while ($i < $length) { 
			// pick a random character from the possible ones
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
			// we don't want this character if it's already in the password
			if (!strstr($password, $char)) { 
			  $password .= $char;
			  $i++;
			}
		}
		
		return $password;
	}
	
	//OOP pattern methods
    public static function getInstance()
    {
		if (!isset(self::$instance)) {
            self::$instance = new Security();
        } 
        return self::$instance;
    } 
}

?>

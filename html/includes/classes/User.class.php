<?php
/*
 File:  User.class.php
 Author: James Kneeland
 Purpose: User information 
 Note: 
*/
require_once(getEnv("DOCUMENT_ROOT") . "/includes/db/users.inc.php");


class User{
	private $ID;
	
	private $classID;
	private $className;
	
	private $firstName;
	private $lastName;
	private $email;
	private $country;
	private $affiliation;
	private $activationKey;
	private $noNotifications;
	
	
	private $modified = false;
	private $deleted = false;
	
	function __construct($firstName = null, $lastName = null, $email = null, $country = null, $affiliation = null){
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->email = $email;
		$this->country = $country;
		$this->affiliation = $affiliation;
	}
	
	function __destruct(){
		//save if needed 
		if($this->modified && (!$this->deleted)){
			$this->save();
		}
	}
	
	
	public static function create($firstName, $lastName, $classID, $email, $country, $affiliation, $password = ""){
		
		$uid = user_add($firstName, $lastName, $classID, $email, $country, $affiliation, $password);
		if($uid == false){
			throw new Exception("Unable to create user");
		}
		$user = User::loadByID($uid);
		return $user;
	}
	
	
	public static function loadByID($userID){
		$data = user_details_get($userID, false);
		
		if($data == false){ //validate 
		  return false;//error
		}
		
		$user = new User($data['firstname'], $data['lastname'],$data['email'], $data['country'], $data['affiliation']);
		$user->ID = $userID;
		$user->classID = $data['userclass_id'];
		$user->noNotifications = false; //$data['no_notifications'];
		return $user;
	
	}
	
	public static function loadByEmail($email){
		$data = user_details_get($email, true);
		
		if($data == false){ //validate
		  return false;//error
		}
		
		$user = new User($data['firstname'], $data['lastName'],$data['email'], $data['country'], $data['affiliation']);
		$user->ID = $data['id'];
		$user->classID = $data['userclass_id'];
		return $user;
	}
	
	public function setFirstName($firstName){
		$this->firstName = $firstName;
		$this->modified = true;
	}
	
	public function getFirstName(){
		return $this->firstName;
	}
	
	
	public function setLastName($lastName){
		$this->lastName = $lastName;
		$this->modified = true;
	}
	
	public function getLastName(){
		return $this->lastName;
	}
	
	
	public function setEmail($email){
	
		//verify that email is not already taken
			//if invalid or taken by another user, throw error
	
		$this->email = $email;
		$this->modified = true;
	}
	
	public function getEmail(){
		return $this->email;
	}
	
	
	public function setCountry($country){
		$this->country = $country;
		$this->modified = true;
	}
	
	public function getCountry(){
		return $this->country;
	}
	
	
	public function setAffiliation($affiliation){
		$this->affiliation = $affiliation;
		$this->modified = true;
	}
	
	public function getAffiliation(){
		return $this->affiliation;
	}
	
	public function getClassName(){
		if(!isset($this->className));{
			$classList = user_class_list();
			foreach($classList as $class){
				if($class['id'] == $this->getClassID()){
					$this->className = $class['name'];
					break;
				}
			}
		}
		return $this->className;
	}
	
	public function getClassID(){
		return $this->classID;
	}
	
	public function setClassID($cid){
		$this->classID = $cid;
		//cached value for className is no longer valid
		unset($this->className);
		$this->modified = true;
	}
	
	
	public function getID(){
		return $this->ID;
	}
	
	public function setPassword($password){
		//insert directly into database
		user_password_update($this->ID, $password);
	}
	
	public function activate($activationKey){
		if($this->getActivationKey() != $activationKey){
			//invalid activation key
		}elseif($this->isActivated()){
			//already validated 
		}else{
			//activate
			user_activate($activationKey);
		}
	}
	
	public function isActivated(){
		//query database
		//TODO :  write db function for this
		return true;
	}
	
	public function notifications(){
	}

	public function getActivationKey(){
		if(!isset($this->activationKey)){
			$this->activationKey = user_activate_get_key($this->ID);
		}
		return $this->activationKey;
	}
	
	public function delete(){
		$this->deleted = true;
		//remove from database
		user_delete($this->ID);
	}
	
	public function save(){
		//save into database
		user_details_update($this->ID, $this->firstName, $this->lastName, $this->classID, $this->email, $this->country, $this->affiliation);
		//no unsaved state now
		$this->modified = false;
	}
}
?>

<?
include_once(dirname(__FILE__) . "/../db/session.inc.php");

class Security{
	//state vars
	protected $_state_is_logged_in = false;
	protected $_state_session_id = null;

	//cached and generated user information
	protected $_user_ip_address;
	protected $_user_referer;
	protected $_user_origin;
	protected $_user_name;
	protected $_user_id;
	protected $_user_email;
	
	//user permission level
	protected $_user_class_id;
	protected $_user_class_name;
		 
	
	//returns whether the session exists and is still valid 
	function _session_is_valid(){
		return session_is_valid($this->_state_session_id);
	}
	
	//returns information about the session
	protected function _session_update_info(){
		$info = session_info($this->_state_session_id);
		$this->_user_name = $info['name'];
		$this->_user_id = $info['user_id'];
		$this->_user_email = $info['email'];
		$this->_user_class_id = $info['userclass_id'];
		$this->_user_class_name = $info['userclass_name'];
	}
	//returns whether the user is logged in and that login is valid
	public function is_logged_in(){
		return $this->_state_is_logged_in;
	}
	
	
	
	public function login($email, $password){
		$result = session_login($email, $password, $this->_state_session_id, $this->_user_ip_address);
		if($result == true){
			$this->_state_is_logged_in = true;
			$this->_session_update_info();
		}
		return $result;
	}
	
	public function logout(){
		$result = session_logout($this->_state_session_id);
		$this->_state_is_logged_in = false;
		session_destroy();
		return true;
	}
	
	public function forward($url){
		header("Location: $url");
	}
	
	//accesssor method
	public function get_user_name(){
		return $this->_user_name;
	}
	
	public function get_user_id(){
		return $this->_user_id;
	}
	public function get_user_email(){
		return $this->_user_email;
	}
	
	public function get_user_referer(){
		return $this->_user_referer;
	}
	
	function get_user_ip_address(){
		return $this->_user_ip_address;
	}
	
	public function get_user_class_id(){
		return $this->_user_class_id;
	}
	
	public function get_user_class_name(){
		return $this->_user_class_name;
	}
	
    public function getInstance ()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new Security();
        } 
        return $instance;
    } 
	
		//initialize for use 
	function Security(){
		session_save_path("/tmp/sessions");
		session_start(); 
		$this->_state_session_id = session_id();
		//change this to actually validate 
		$this->_state_is_logged_in = $this->_session_is_valid();
		
		//HTTP vars
		$this->_user_referer = $_SERVER['HTTP_REFERER'];
		$this->_user_ip_address = $_SERVER['REMOTE_ADDR'];
		
		
		//This is the original referer to the site
		if(!isset($_SESSION['origin'])){
			echo $_SESSION['origin'];
			$_SESSION['origin'] = $this->_user_referer;
		}
		// Information from database if user is logged in
		if($this->_state_is_logged_in){
			$this->_session_update_info();
		}
	}
}

$auth = Security::getInstance();
?>
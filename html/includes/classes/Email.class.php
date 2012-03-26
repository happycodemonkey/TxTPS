<?php

require_once(dirname(__FILE__) . "/../db/users.inc.php");
require_once(dirname(__FILE__) . "/../db/data.inc.php");
require_once(dirname(__FILE__) . "/User.class.php");


class Email{
	protected $msg;
	protected $subject = "Texas Test Problem Server";
	protected $dest;
	protected $headers;
	protected $sender = 'Texas Test Problem Server <do_not_reply@tps.tacc.utexas.edu>';
	protected $user;
	
	public function __construct(User $user){
	  $this->user = $user;
	  $this->dest = $user->getEmail();
	}
	
	public function getMessage(){
		return $this->msg;
	}
	
	public function setMessage($msg){
		$this->msg = $msg;
	}
	
	public function getSubject(){
		return $this->subject;
	}
	
	public function setSubject($subject){
		$this->subject = $subject;
	}
	
	public function setDestination($dest){
		$this->dest = $dest;
	}
	
	public function getDestination(){
		return $this->dest;
	}
	
	public function setHeaders($headers){
		$this->name = $name;
	}
	
	public function getHeaders(){
		return $this->headers;
	}
	
	public function send(){
		$combined_headers = "From: $this->sender \r\nReply-To: $this->sender \r\n" . $this->headers;
		mail($this->getDestination(),$this->getSubject(),$this->getMessage(),$combined_headers);
	}
}


class AccountConfirmationEmail extends Email{
  protected $password;

  public function __construct(User $user,$password){
    $this->password = $password;
    parent::__construct($user);
  }
  
  public function send(){
    
    parent::setSubject("TxTPS: Account Confirmation");

    $code = user_activate_get_key($this->user->getID());

    $link = "https://tps.tacc.utexas.edu/about/signup.php?action=activate&code=". $code;
    
    
    $msg  ="Before you can use your account, you must confirm that you have access this email address.\n\n";
    $msg .= "Click on the link below or copy and paste it into your browser to confirm your account. You will be unable to use your account until this has been completed.\n\n";
    $msg .="$link\n\n";
    $msg .="\n\n";
    $msg .="Your password is: " . $this->password . "\n\n";
    $msg .="You may change this password at any time by visiting https://tps.tacc.utexas.edu/profile/profile.php\n\n";
    
    parent::setMessage($msg);
    parent::send();
  }  
  
}


class PasswordResetEmail extends Email{
	
  protected $password;
  public function __construct(User $user,$password){
    $this->password= $password;
    parent::__construct($user);
  }

  public function send(){
    
    parent::setSubject("TxTPS: Password Reset");
    
    $msg  ="Your TxTPS account password has been reset to a randomly generated password. You will now be able to login in to your account using the password below.\n\n";
    $msg .="\n\n";
    $msg .="Your password is: " . $this->password. "\n\n";
    $msg .="You may change this password at any time by visiting https://tps.tacc.utexas.edu/profile/profile.php\n\n";
    $msg .="\n\n";
    $msg .="If you did not request this change, please contact support at https://tps.tacc.utexas.edu/about/contact.php\n\n";
    
    parent::setMessage($msg);
    parent::send();
  }

}


class JobRequestedEmail extends Email{
	
  protected $product_id;
  public function __construct(User $user,$id){
    $this->product_id= $id;
    parent::__construct($user);
 }

  public function send(){

    $link = "";
    
    parent::setSubject("TxTPS: Requested Generation Job Queued");

    $msg  ="Your recent generation request has been recieved and will be processed shortly when space in the queue becomes free.  You will be notified via email when your build request has been completed.";
    $msg .="\n\n";
   
    parent::setMessage($msg);
    parent::send();
  }

}


class JobCompleteEmail extends Email{
	
  protected $product_id;
  public function __construct(User $user,$id){
    $this->product_id= $id;
    parent::__construct($user);
 }

  public function send(){

    $pquery    = sprintf("SELECT * FROM product WHERE id=%d",$this->product_id);
    $info_list = db_query($pquery,true);
    $info = $info_list[0];
    var_dump($info);
    $identifier = $info['identifier'];
    

    $link = "https://tps.tacc.utexas.edu/problems/$identifier";
    
    parent::setSubject("TxTPS: Requested Generation Job Complete");

    $msg  ="Your recent generation request has been completed and is now ready to be downloaded.  To download, click on the link below or copy and paste it into your browser's address bar.\n\n";
    $msg .="\n\n";
    $msg .="$link\n\n";
   
    parent::setMessage($msg);
    parent::send();
  }

}



class JobFailureEmail extends Email{
	
  protected $product_id;
  public function __construct(User $user,$id){
    $this->product_id= $id;
    parent::__construct($user);
  }

  public function send(){
    
        
    parent::setSubject("TxTPS: Requested Generation Job Failed");
    
//    $query ="SELECT generator.name as generator_name, collection.name as collection_name, identifier from product left join (generator, collection) on (product.generator_id = generator.id AND generator.collection_id = collection.collection_id) WHERE product.id = " . $this->product_id;     

    $query = "SELECT * from product WHERE id=". $this->product_id . ";";  
  
    $result = db_query($query,true);
    $result = $result[0];
    
    $identifier= $result['identifier'];


    $msg  ="Unfortunately, your recent generation request resulted in an error. This may be due to incorrect generator inputs or may also be the result of a server fault. This error has been logged and the cause will be investigated. \n\n";
    $msg .="You may be contacted regarding this error.\n\n";
    
    $msg .= "For more information, you can check the status of the problem at the URL below:\n\n";
    $msg .= "http://tps.tacc.utexas.edu/problems/" . $identifier;
    $msg .="\n\n";

    $msg .="\n\n";
    //$msg .="Please check your inputs errors. If you are certain there are no errors, feel free to resubit.";
   
    parent::setMessage($msg);
    parent::send();
  }

}


?>

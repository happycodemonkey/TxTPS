<?php

require_once(dirname(__FILE__) . "/../db/users.inc.php");
require_once(dirname(__FILE__) . "/../db/data.inc.php");

define ("EMAILER_SENDER","Test Problem Server <do_not_reply@lovett.tacc.utexas.edu>");


function emailer_send_account_confirmation($userid, $password){
  $info = user_details_get($userid);
  $subject = "TPS Account Confirmation";
  $code = user_activate_get_key($userid); 
  if($code == false){
	$code = 0;
  }
  $link = "http://lovett.tacc.utexas.edu/beta/signup.php?action=activate&code=". $code;
  $headers = 'From: '. EMAILER_SENDER . "\r\n" .
    'Reply-To: '. EMAILER_SENDER . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
  $msg  = <<<MSG
Before you can use your account, you must click on the link below to confirm your account. You will be unable to use your account until this has been completed. 
  
  $link

Your password is: $password

MSG;
mail($info['email'],$subject,$msg,$headers);
}


function emailer_send_job_complete($userid, $jobid){
  $info = product_get($jobid);
  $subject = "TPS Build Complete";
  $code = ""; 
  $link = "http://lovett.tacc.utexas.edu/beta/pickup.php?id=". $identifer;
  $headers = 'From: '. EMAILER_SENDER . "\r\n" .
    'Reply-To: '. EMAILER_SENDER . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
  $msg  = <<<MSG
Your recent generation request has been completed and is now ready to be downloaded.  To download, click on the link below or copy and paste it into your browser's address bar.
 
  $link

MSG;
mail($info['email'],$subject,$msg,$headers);

}

function emailer_send_job_received($userid,$jobid){
  $info = user_details_get($userid);
  $subject = "TPS Build Requested";
  $headers = 'From: '. EMAILER_SENDER . "\r\n" .
    'Reply-To: '. EMAILER_SENDER . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
  $msg  = <<<MSG
Your recent generation request has been received and will be processed shortly when space in the queue becomes free.  You will be notified when 
your build request has been completed. 
 
MSG;
mail($info['email'],$subject,$msg,$headers);
}
?>

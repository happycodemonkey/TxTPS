<?php
include_once("../includes/db/users.inc.php");
//require_once("./includes/util/security.inc.php");
require_once("../includes/classes/Security.class.php");
require_once("../includes/classes/User.class.php");

/* Require the user to be logged in */
$security = Security::getInstance();
if(!$security->isLoggedIn()){
	Security::forward("/login.php");
}


//state vars
$user = $security->getUser();
$submitted = false;
$changePassword = false;
$error = false;
$message;



//page data
$uid = $user->getID();
$firstName = (isset($_REQUEST['firstname']))?$_REQUEST['firstname']:$user->getFirstName();
$lastName = (isset($_REQUEST['lastname']))?$_REQUEST['lastname']:$user->getLastName();
$email = $user->getEmail(); //user not allowed to change email 
$affiliation = (isset($_REQUEST['affiliation']))?$_REQUEST['affiliation']:$user->getAffiliation();
$country = (isset($_REQUEST['country']))?$_REQUEST['country']:$user->getCountry();
$userClass = $user->getClassName();




//validate submission
if(isset($_REQUEST['firstname']) && isset($_REQUEST['lastname']) 
	&& isset($_REQUEST['affiliation']) && isset($_REQUEST['country'])){
	
	$submitted = true;
	
	//check length
	if(strlen($_REQUEST['firstname']) < 1){
		$error = true;
		$message = "First name must be at least one letter";
	}elseif(strlen($_REQUEST['lastname']) < 1){
		$error = true;
		$message = "Last name must be at least one letter";
	}elseif(strlen($_REQUEST['affiliation']) < 1){
		$error = true;
		$message = "Affiliation must be at least one letter";
	}elseif(strlen($_REQUEST['country']) < 1){
		$error = true;
		$message = "Country must be at least one letter";
	}

} 


//if the above are correct, look for password changes
if($submitted && !$error){
	if((strlen($_REQUEST['password']) > 0) && isset($_REQUEST['password']) && isset($_REQUEST['passwordc'])){
		
		//check that the new passwords are the correct length and that they're equal
		if((strlen($_REQUEST['password'])< 8)){
			$error = true;
			$message = "Password must be at least 8 characters long";
		}elseif($_REQUEST['password'] != $_REQUEST['passwordc']){
			$error = true;
			$message = "The same password must be entered twice";
		}else{
			$changePassword = true;
		}
	}

}


//update database
if($submitted && !$error){
	$user->setFirstName($firstName);
	$user->setLastName($lastName);
	$user->setAffiliation($affiliation);
	$user->setCountry($country);
	$user->save();
}

if($changePassword && !$error){
	$user->setPassword($_REQUEST['password']);
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<link  rel="stylesheet" type="text/css" href="/css/main.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Test Problem Server</title>
</head>
<body>
<div id="wrapper">
	<div id="titlebar">
		<?php include("../includes/templating/title.inc.php"); ?>
	</div>

	<div id="login">
                <?php require("../includes/templating/login.inc.php"); ?>
        </div>
	<div id="menu">
		<?php include("./menu.inc.php"); ?>
	</div>
	<div id="sidemenu">
		<?php include("./submenu.inc.php"); ?>
	</div>
	<div id="content">
		<h1>Profile</h1>
		<span style="color:red; font-weight:bold;"><?php if($error){ echo $message; } ?></span>
		<form method="post">
			<input type="hidden" name="action" value="edit">
			<input type="hidden" name="step" value="1">
			<input type="hidden" name="id"	value="<?php echo $uid?>">
			<table>
			<tr>
				<td>First name:</td>
				<td><input type="text" name="firstname" value="<?php echo $firstName; ?>"></td>
			</tr>	
			<tr>
				<td>Last name:</td>
				<td><input type="text" name="lastname" value="<?php echo $lastName; ?>"></td>
			</tr>

			<tr>
				<td>Country:</td>
				<td><input type="text" name="country" value="<?php echo $country;?>"></td>
			</tr>
			<tr>
				<td>Affiliation:</td>
				<td><input type="text" name="affiliation" value="<?php echo $affiliation; ?>"></td>
			</tr>
			<tr>
				<td>User Class</td>
				<td><input disabled="true" type="text" name="class" value="<?php echo $userClass; ?>"></td>
			</tr>
			<tr>
				<td>E-Mail</td>
				<td><input disabled="true" type="text" name="email" value="<?php echo $email; ?>"></td>
			</tr>
				<tr>
				<td>Password (leave blank to keep same) </td>
				<td><input type="password" name="password" value=""></td>
			</tr>
			<tr>
				<td>Password again</td>
				<td><input type="password" name="passwordc" value=""></td>
			</tr>
			</table>
			<p><input type="submit" value="Save"></p>
		</form>
	</div>
	<div id="footer">
	</div>
</div>
</body>
</html>

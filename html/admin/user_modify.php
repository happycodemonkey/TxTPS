<?php
include_once("../includes/db/users.inc.php");
require_once("../includes/classes/Security.class.php");



$security=Security::getInstance();

if(!$security->isLoggedIn() || $security->getClassName() !== "Admin"){
  Security::forward("../login.php");
 }








$page_header = "Modify User";
$action = $_REQUEST['action'];
switch($action){
	case "edit":
	$page_header = "Edit User";
	break;
	
	case "resetpassword":
	$pade_header = "Edit User";
	break;
	
	case "delete":
	$page_header = "Delete User";
	break;
	
	case "add":
	default:
	$page_header = "Add User";
	break;
}



function add(){
	$step = 0;
	if(isset($_REQUEST['step']) AND is_numeric($_REQUEST['step'])){
		$step = $_REQUEST['step'];
	}
	if($step == 0){
	?>
		<form method="post">
		<input type="hidden" name="action" value="add">
		<input type="hidden" name="step" value="1">
		<table>
		<tr>
			<td>Username:</td>
			<td><input type="text" name="username"></td>
		</tr>
		<tr>
	        	<td>Password</td>
			<td><input type="text" name="password"></td>
		</tr>
		<tr>
	        	<td>E-Mail</td>
			<td><input type="text" name="email"></td>
		</tr>
		<tr>
	        	<td>User Class</td>
			<td>
				<select name="class">
				  <option value="1">Standard</option>
				  <option value="2">Power User</option>
				  <option value="3">TACC</option>
				  <option value="4">TeraGrid</option>
				  <option value="5">Admin</option>
				</select>
			</td>
		</tr>

		</table>
		<p><input type="submit" value="Submit"></p>
		</form>
	<?php
	}elseif($step == 1){
		echo "Step 1";
		//insert the user into the database 
		user_add($_REQUEST['username'], $_REQUEST['class'], $_REQUEST['email'], $_REQUEST['password']);
		?>
			<p>The user was added to the database. <a href="user.php">Click here</a> to return</p>
		<?
	}
}

function edit(){
	$step = 0;
	$id = $_REQUEST['id'];
	if(isset($_REQUEST['step']) AND is_numeric($_REQUEST['step'])){
		$step = $_REQUEST['step'];
	}
	if($step == 0){
	//get user data
	$info = user_details_get($id);
	?>
		<form method="post">
		<input type="hidden" name="action" value="edit">
		<input type="hidden" name="step" value="1">
		<input type="hidden" name="id"	value="<?php echo $id?>">
		<table>
			<tr>
				<td>First name:</td>
				<td><input type="text" name="firstname" value="<?php echo $info['firstname'];?>"></td>
			</tr>	
			<tr>
				<td>Last name:</td>
				<td><input type="text" name="lastname" value="<?php echo $info['lastname'];?>"></td>
			</tr>
				
			<tr>
				<td>Country:</td>
				<td><input type="text" name="country" value="<?php echo $info['country'];?>"></td>
			</tr>
			<tr>
				<td>Affiliation:</td>
				<td><input type="text" name="affiliation" value="<?php echo $info['affiliation'];?>"></td>
			</tr>
			<tr>
				<td>User Class</td>
				<td>
					<?php 
					$classlist = user_class_list();
					?>
					<select name="class">
						<?php
						foreach($classlist as $class){
							$selected = "";
							if($class['id'] == $info['userclass_id']){
								$selected = "selected=\"true\"";
							}
						?>
						  <option value="<?php echo $class['id']?>" <?php echo $selected ?>><?php echo $class['name']?></option>
						<?php
						}//end foreach
						?>
					</select>
				</td>			
			</tr>
			<tr>
				<td>E-Mail</td>
				<td><input  type="text" name="email" value="<?php echo $info['email'];?>"></td>
			</tr>
		
		</tr>

		</table>
		<p><input type="submit" value="Submit"></p>
		</form>
	<?php
	}elseif($step == 1){
		//insert the user into the database 
		//user_details_update($user_id, $firstname, $lastname, $class_id, $email, $country, $affiliation){
		user_details_update($_REQUEST['id'], $_REQUEST['firstname'], $_REQUEST['lastname'], $_REQUEST['class'], $_REQUEST['email'], $_REQUEST['country'], $_REQUEST['affiliation']);
		?>
			<p>The user's information has been updated in the database. <a href="user.php">Click here</a> to return</p>
		<?
	}
}

function reset_password(){
	
}

function delete(){
	$step = 0;
	$id = $_REQUEST['id'];
	if(isset($_REQUEST['step']) AND is_numeric($_REQUEST['step'])){
		$step = $_REQUEST['step'];
	}
	if($step == 0){
	$info = user_details_get($id);
	?>
		<form method="post">
		<input type="hidden" name="action" value="delete">
		<input type="hidden" name="step" value="1">
		<input type="hidden" name="id"	value="<?php echo $id?>">
		<p>Are you sure you want to delete user: <?php echo	$info['firstname'] . "&nbsp;" . $info['lastname']  . "&nbsp;(" . $info['email'] . ")"?>?,
		<p><input type="submit" name="submit" value="Delete"> <input type="submit" name="submit" value="Cancel"></p>
		</form>
	<?php
	}elseif($step == 1){
		//insert the user into the database 
		if($_REQUEST['submit'] == "Delete"){
			user_delete($id);
			?>
			<p>The user was successfully deleted. <a href="user.php">Click here</a> to continue.</p>
			<?php
		}else{
			?>
			<p>The user was NOT deleted. <a href="user.php">Click here</a> to continue.</p>
			<?php
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link  rel="stylesheet" type="text/css" href="../css/main.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Test Problem Server</title>
</head>
<body>
<div id="wrapper">
	<div id="titlebar">
		<?php include("../includes/templating/title.inc.php"); ?>
	</div>
	<div id="menu">
		<?php include("../includes/templating/adminmenu.inc.php"); ?>
	</div>

	<div id="content">
			<h1><?php echo $page_header ?></h1>
		<?
		switch($action){
			case "edit":
			edit();
			break;
			
			case "resetpassword":
			reset_password();
			break;
			
			case "delete":
			delete();
			break;
			
			case "add":
			default:
			add();
			break;
		}
		?>
		
	</div>
	<div id="footer">
	</div>
</div>
</body>
</html>

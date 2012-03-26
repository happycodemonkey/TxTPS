<?php
include_once("../includes/db/users.inc.php");
require_once("../includes/classes/Security.class.php");


$security = Security::getInstance();
if(!$security->isLoggedIn() || $security->getClassName() !== "Admin"){
  Security::forward("../login.php");
}


$user_list = user_list();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<link rel="stylesheet" type="text/css" href="../css/main.css">
<style type="text/css">
th,td{
	padding:.25em 1em .25em 1em;
}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Test Problem Server</title>
</head><body>
<div id="wrapper">
	<div id="titlebar">
		<?php include("../includes/templating/title.inc.php"); ?>
	</div>
	<div id="menu">
		<?php include("../includes/templating/adminmenu.inc.php"); ?>
	</div>

	<div id="content">
		<h1>User Management</h1>

		<div>
		<p>This page allows you to <a href="http://lovett.tacc.utexas.edu/beta/admin/user_modify.php">add</a>, modify, delete, and otherwise manage users.</p><p>
		</p></div>
		<div>
		<?php
		if($user_list != false){
		?>
			<table>
				<tbody><tr>
					<th>Username</th>
					<th>User&nbsp;Class</th>
					<th>E-Mail</th>
					<th>Modify</th>
					<th>Delete</th><th>
				</th></tr><tr>
				</tr>
				<?php
				foreach($user_list as $user){
				?>
					<tr>
						<td><?php echo $user['firstname'] . "&nbsp;" . $user['lastname'] ?></td>
						<td><?php echo $user['class']?></td>
						<td><?php echo $user['email']?></td>
						<td><form method="post" action="user_modify.php"><input name="id" value="<?php echo $user['id']?>" type="hidden"><input type="hidden" name="action" value="edit"><input value="Modify" type="submit"></form></td>
						<td><form method="post" action="user_modify.php"><input name="id" value="<?php echo $user['id']?>" type="hidden"><input type="hidden" name="action" value="delete"><input value="Delete" type="submit"></form></td>
					</tr>
				<?php
				} //end of foreach
				?>			
				</tbody>
			</table>
			<span style="margin: 0.5em; display: inline-block; float: left;"><a href="#">First</a></span>
			<span style="margin: 0.5em; display: inline-block; float: left;"><a href="#">Previous</a></span>
			<span style="margin: 0.5em; display: inline-block; float: right;"><a href="#">Last</a></span>
			<span style="margin: 0.5em; display: inline-block; float: right;"><a href="#">Next</a></span>
		<?php
		}else{//end if
			echo "<p><b>No users found</b></p>";
		}//end else
		?>
		</div>
	</div>
	<div id="footer">
	</div>
</div>
</body></html>

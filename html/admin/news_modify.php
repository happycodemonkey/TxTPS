<?
include_once("../includes/db/users.inc.php");
include_once("../includes/db/generators.inc.php");
include_once("../includes/db/data.inc.php");
require_once("../includes/classes/Security.class.php");

$security = Security::getInstance();
if(!$security->isLoggedIn() || $security->getClassName() !== "Admin"){
  Security::forward("../login.php");
 }


$story_id;
$story = array();
$query = "";
$editing = false;


if(count ($_POST)  > 0){
  $title=htmlentities($_POST['title']);
  $body=htmlentities($_POST['body']);
  $id=$_POST['id'];
  if(isset($_POST['id'])){
    //update
    
    $query = "UPDATE news SET title='$title', body='$body' WHERE id=$id";
  

  }else{
    //insert
    $query = "INSERT INTO news (title,body,timestamp) VALUES('$title','$body',UNIX_TIMESTAMP())";
    
  }

  db_query($query);
  Security::forward("./news.php");

 }

if(isset($_GET['delete']))
  {
    $story_id = $_GET['delete'];
    $query = "DELETE FROM news WHERE id=" . $story_id;
    db_query($query,true);

    Security::forward("./news.php");

  }



if(isset($_GET['edit']))
  {
    $story_id = $_GET['edit'];
    $editing = true;

    $query = "SELECT * FROM news WHERE id=" . $story_id;
    $stories= (db_query($query,true));
    $story = $stories[0];

  }


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link  rel="stylesheet" type="text/css" href="../css/main.css" />
<style type="text/css">
th,td{
	padding:.125em .5em .125em .5em;
}
.panelHeader{
	font-weight:bold;
	font-size:medium;
	cursor:pointer;
}
.mdHover{
	color:#666;
}
.mdSelected{
	color:#00CCFF;
}

</style>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Test Problem Server</title>
<script src="../src/rico.js" type="text/javascript"></script>
<script type='text/javascript'>
Rico.loadModule('Accordion');

Rico.onLoad( function() {
  new Rico.Accordion( $$('div.panelHeader'), $$('div.panelContent'),
                      {panelHeight:300, hoverClass: 'mdHover', selectedClass: 'mdSelected'});
});
</script>
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
  <h1>Modify News (
		   <?php if($editing)
		     { echo 'Editing "' . $story['title'] . '"'; } 
		   else 
		     { echo "New Item" ;}
		   ?> 
		   )</h1>
	<form method="post">
  <?php
        if($editing){
	  echo '<input type="hidden" name="id" value="' . $story['id'] . '">';
	}
  ?>

  <h2>Title:</h2>
	<input type="text" name="title" value="<?php echo $story['title'] ?>">

  <h2>Body:</h2>
        <div>
        <textarea name="body"><?php echo $story['body'] ?></textarea>
        </div>
<br>
	<input type="submit" value="Update/Save">
  
  </form>

	</div>
	<div id="footer">
	</div>
</div>
</body>
</html>

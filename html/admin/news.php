<?
include_once("../includes/db/users.inc.php");
include_once("../includes/db/generators.inc.php");
include_once("../includes/db/data.inc.php");
require_once("../includes/classes/Security.class.php");

$security = Security::getInstance();
if(!$security->isLoggedIn() || $security->getClassName() !== "Admin"){
  Security::forward("../login.php");
 }

$query = "SELECT * FROM news ORDER BY timestamp DESC";
$stories = db_query($query,true);

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
		<h1>Server News</h1>
<?php
        $count = 0;
foreach($stories as $story){
  $count++;
  if(count > 10){break;}
  ?>
    <h2><?php echo html_entity_decode($story['title']) ?><h2>
        <div>

       <?php echo html_entity_decode($story['body']) ?>

        </div>

	   <h3>[<a href="news_modify.php?edit=<?php echo $story['id']?>">Edit</a>] [<a href="news_modify.php?delete=<?php echo $story['id']?>">Delete</a>]</h3>
  <br><br>
  <?php
		   }//end loop
  ?>


  <br><br><br>

  <h2><a href="news_modify.php">New Item</a></h2>

	</div>
	<div id="footer">
	</div>
</div>
</body>
</html>

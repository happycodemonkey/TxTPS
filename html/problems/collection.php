<?
require_once("../includes/classes/Security.class.php");
require_once(dirname(__FILE__) . "/../includes/db/generators.inc.php");
require_once(dirname(__FILE__) . "/../includes/db/tags.inc.php");
require_once(dirname(__FILE__) . "/../includes/db/comments.inc.php");
require_once(dirname(__FILE__) . "/../includes/classes/Collection.class.php");

$security = Security::getInstance();
$id = $_REQUEST['id'];
$generator_list = null;
$start = 0;
$limit = 10000;
$collection_list = generator_collection_list();



if(isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])){
  $generator_list = generator_list("id",$start,$limit, $_REQUEST['id']);
 }else{
  $generator_list = generator_list("id",$start,$limit);
 }
$collection = Collection::loadByID($_REQUEST['id']);
$db_info = generator_collection_get($_REQUEST['id']);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>

<!--
 /* Font Definitions */
@font-face
{font-family:"Times New Roman";
 panose-1:0 2 2 6 3 5 4 5 2 3;
}
/* Style Definitions */
p.MsoNormal, li.MsoNormal, div.MsoNormal
{margin:0in;
  margin-bottom:.0001pt;
  font-size:12.0pt;
  font-family:"Times New Roman";}
table.MsoNormalTable
{font-size:10.0pt;
  font-family:"Times New Roman";}
p.MTDisplayEquation, li.MTDisplayEquation, div.MTDisplayEquation
{margin:0in;
  margin-bottom:.0001pt;
  font-size:12.0pt;
  font-family:"Times New Roman";}
@page Section1
{size:8.5in 11.0in;
 margin:1.0in 1.25in 1.0in 1.25in;
}
div.Section1
{page:Section1;}
-->
</style>

<!-- MP HEAD( -->
<style>
	      .MPPH { visibility:hidden; page-break-inside:avoid }
	      .MPPHSpan { text-indent:0 }
	      sub,sup { font-size:x-small }
#MPDpiSpan { position:absolute; top:1in; left:1in; width:100px; border:none; visibility:hidden }
	      .MPEntity { font-family:'Times New Roman',Times,serif }
</style>
<style media="screen">
	      .MPScreenEqn { position:absolute; visibility:visible; z-index:98 }
	      .MPPrintEqn { display:none }
	      .MPScreenPH { }
	      .MPPrintPH { display:none }
	      .MPPopup { position:absolute; visibility:hidden; z-index:99; top:0; left:0; text-indent:0; cursor:hand; 
	      border:solid blue 1px; padding:5px; background-color:#FFFFCC; 
	      filter:progid:DXImageTransform.Microsoft.DropShadow(color=#444444,offx=3,offy=3,positive=1) 
		   }
	      .MPPopupNoBg { position:absolute; visibility:hidden; z-index:99; top:0; left:0; text-indent:0; cursor:hand; 
	      border:solid blue 1px; padding:5px; 
	      filter:progid:DXImageTransform.Microsoft.DropShadow(color=#444444,offx=3,offy=3,positive=1) 
		   }
</style>
<style media="print">
	      .MPScreenEqn { display:none }
	      .MPPrintEqn { position:absolute; visibility:visible }
	      .MPScreenPH { display:none }
	      .MPPrintPH { }
	      .MPPopup { display:none }
	      .MPPopupNoBg { display:none }
</style>
<script language="javascript" src="Images/mathpage.js"></script><style> 
	      .MPNNCode { display:none } 
	      sub,sup { font-size:.7em } 
</style> 
<script language="javascript">
	      if (typeof DSMP == 'undefined') {
		alert('MathPage Javascript file missing; equations and symbols will not display.');
		var DSMP = new Object;
		MPBodyInit=MPSetEqnAttrs=MPSetChAttrs=MPEquation=MPInlineChar=MPDeleteCode=MPNNCalcTopLeft=MPHidePopup=MPShowPopup=MPNNSelectScreenEqn=MPWebEQApplet=MPTechexplorerObject=function(){};
		DSMP.gEmptySrc=DSMP.gPlaceholderHeight=DSMP.gPlaceholderWidth=DSMP.gPlaceholder2Height=DSMP.gNNPopupBgColor=DSMP.gPopupEqnSrc=DSMP.gPopupEqnPadding=DSMP.gNNLayerTop=DSMP.gNNLayerLeft=DSMP.gScreenEqnSrc=DSMP.gScreenEqnWidth=DSMP.gScreenEqnHeight=DSMP.gPrintEqnSrc=null;
	      }
	      DSMP.gPageVersion = '1.1';
	      DSMP.gMaxCharCompat = 1;
	      DSMP.gGenMathZoom = 1;
	      DSMP.gPopupEqnBgColorDefault = '#FFFFCC';
	      DSMP.gPopupEqnPaddingDefault = 5;
	      DSMP.gPlaceholderPadding = 4;
	      DSMP.gOldJSMessage = 'Warning: this MathPage document requires a newer JavaScript file and may not display correctly.';
	      DSMP.gCompatMessage = 'Warning: this MathPage document was generated for Mac IE5 or later only and may not display correctly.';
	      DSMP.gMinBrowserMessage = 'Warning: MathPage requires a version 4 or later browser.';
	      DSMP.gHidePopupMessage = 'Click on a MathZoom equation to dismiss it, or shift-Click to close all.';
	      DSMP.gShowPopupMessage = 'Click on an equation to show the enlarged MathZoom version.';
</script>



<link rel="stylesheet" type="text/css" href="/css/main.css">
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
	      <div style="display:block;float:left;width:35em;">
	      <h1>Step 1: Select Generator Group</h1>

                <?php 
	      if($collection_list == false){
		echo "<p><b>No generator groups found</b></p>";
	      }else{
 ?>

		<div>
			<table>
			<tbody><tr>
				<th style="padding:.25em 1em .25em 1em;">Group Name</th>

				<th style="padding:.25em 1em .25em 1em;">Generator List</th>
			</th></tr><tr>
			</tr>
			<?php
			foreach($collection_list as $row){
		    
		    //TODO Implement selection of additonal information in the DB code
			?>
			<tr>
				<td style="padding:.25em 1em .25em 1em;"><?php echo $row["name"]?></td>

				<td style="padding:.25em 1em .25em 1em;"><form method="get" action="collection.php"><input name="id" value="<?php echo $row['id']?>" type="hidden"><input value="List Generators" type="submit"></form></td>
			</tr>
			<?php 
			}//end for loop
			?>
		</tbody></table>

		</div>
		
                <?php
		    }//end else
                ?>
	      </div>
	      
	      <div style="display:block;float:left;width:35em;">
	      <h1>Step 2: Select Generator</h1>
	      <h2>Name: <?php echo $db_info['name']; ?></h2>
	    <h2>Description</h2>
	    <p style="width:25em;"><?php echo $db_info['description']; ?></p>
	    <h2>Generators</h2>

	    
	    
	    <div>
	    </p></div>
	    <?php
	    if(($generator_list) !== false){
	      ?>
	      <div>
		<table>
		<tbody><tr>
		<th>Generator Name</th>
		
		<th>Generator Details</th>
		</tr><tr>
		</tr>
		<?php
		foreach($generator_list as $row){
		?>
		<tr>
		   <td><?php echo $row["name"]?></td>
		   
		   <td><form method="get" action="generator.php"><input name="id" value="<?php echo $row['id']?>" type="hidden"><input value="Show Details" type="submit"></form></td>
		   </tr>
		   <?php 
		   }//end for loop
	      ?>
		</tbody></table>
		    </div>
		
		    </div>
		    <?php
		    }else{//end of if(false)

	      ?>

	      <div>
		<p><b>No generators found</b></p>
		</div>
		<?php
		}//end of else






 /*
<h2>Comments</h2>
if($security->isLoggedIn())
  {

    ?>

    <form method="post" action="/comments/post.php">
      <input type="hidden" name="id" value="<?php echo $id?>">
      <input type="hidden" name="type" value="collection">
      <textarea name="message" cols="50" rows="6">
      </textarea>
      <br/>
      <input type="submit" value="Submit Comment"/>
    </form>
      <br />
      <br />
      <br />
      
  <?php

  } //end of submit/loggedin if statement


<table>
<th>
<tr>
<td>#</td>
<td>Comment</td>
<td>Email</td>
<td>Name</td>
</tr>
</th>



$comments = comments_collection_list($id);

$counter = 1;

foreach ($comments as $comment){
  echo "<tr>"; 

  echo "<td>" . $counter . "</td>";
  echo "<td>" . $comment['message'] . "</td>";
  echo "<td>" . $comment['email'] . "</td>";
  echo "<td>" . $comment['name'] . "</td>";

  echo "</tr>";
  $counter++;
}



</table>
*/

?>

</div>
<div id="footer">
  </div>
</div>
</body></html>

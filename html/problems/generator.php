
<?php
require_once("../includes/classes/Generator.class.php");
require_once("../includes/classes/Collection.class.php");
require_once("../includes/classes/Security.class.php");
require_once("../includes/db/data.inc.php");
require_once("../includes/db/comments.inc.php");
require_once("../includes/db/tags.inc.php");
require_once("../includes/util/emailer.inc.php");

$id = $_REQUEST['id'];
$security = Security::getInstance();
$generator = Generator::loadByID($id);
$title = "Generator";
$details = generator_details_get($id);

$collection_list = generator_collection_list();
$collection_id = $details['collection_id'];
$collection_details = generator_collection_get($collection_id);
$collection_name = $collection_details['name'];
$title = "<a href=\"collection.php?id=$collection_id\" > $collection_name </a>";

$description = $generator->getDescription();
$generator_id = $id;
$generator_name = $generator->getName();;
$generator_list = generator_list("id",0,10000, $collection_id);


$subtitle = "<a href=\"generator.php?id=$generator_id\" > $generator_name </a>";

$description = $details['description'];//$generator->getDescription();


$action = $_REQUEST['action'];
$step = $_REQUEST['step'];
$gen_id =  $_REQUEST['id'];

   
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Test Problem Server</title>
<head>





<link  rel="stylesheet" type="text/css" href="/css/main.css" />
<style type="text/css">
th,td{
	padding:.25em 1em .25em 1em;
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
content{
 padding:0;margin:0;
}
</style>
<script src="./src/rico.js" type="text/javascript"></script>
<script type='text/javascript'>
Rico.loadModule('Accordion');

Rico.onLoad( function() {
  new Rico.Accordion( $$('div.panelHeader'), $$('div.panelContent'),
                      {panelHeight:200, hoverClass: 'mdHover', selectedClass: 'mdSelected'});
});
</script>
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





	      <div style="display:block;float:left;width:35em;">
	      <h1>Step 1: Generator Group</h1>
	      
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
	      </div><div style="display:block;float:left;width:35em;">
  <h1>Step 2: Select Generator</h1>
	<h2>Name: <?php echo $collection_name; ?></h2>
            <h2>Description</h2>
	<p style="width:25em"><?php echo $collection_details['description']; ?></p>
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
                    <?php
                    }else{//end of if(false)
          ?>
                <p><b>No generators found</b></p>
                </div>
                <?php
	    }//end of else

?>

        </div>
	<div style="display:block;float:left;width:35em;">
  <h1>Step 3: Generator Details</h1>
        <h2><?php echo "Generator Name</h2><p> " . $subtitle; ?></p>

	<h2>Description</h2>
	<div style="width:30em;"><?php echo $description; ?></div>
	

	<?php

	echo "<h2>Recent History</h2>";
        ?>
	<?php
	$recent_list = product_list_by_generator('created',0,10,$id);
	      if(count($recent_list) > 0 && is_array($recent_list)){
	  echo "<table>\n";
	  echo "<tr>\n";
	  echo "<th>Identifier</th>\n";
	  echo "<th>Creation</th>\n";
	  echo "</tr>\n";
	  foreach($recent_list as $product){
	    echo "<tr>";
	    echo "<td><a href=\"/matrices/matrix.php?id=". $product['identifier']. "\">" . $product['identifier'] . "</a></td>";
	    echo "<td>" . $product['created'] . "</td>";
	    echo "<tr>";
	  }
	  echo "</table>";
	}else{
	  echo "<p><b>No matrices have been generated</b></p>";
	}


        echo "</div>";
	echo '<div style="display:block;float:left;width:35em;">';
        echo "<h1>Step 4: Build</h1>";
	      
	      $disabled = $details['disabled'];
	      if($disabled){ //is disabled 
		
		echo "<p><b>This generator has been temporarily disabled for technical reasons. </p><p> It will be brought back online as soon as possible.</b></p>";
		
	}
	else if($security->isLoggedIn())
	{
	  build();
	}else{
	   echo "<p><b>You must be logged in before building a matrix</b></p>";
	}
        echo "</div>";
        ?>

	






	
	</div>
	<div id="footer">
	</div>
</div>
</body>
</html>


<?php



function build(){
  global $security;
  global $step;
  global $gen_id;

    
	$args = array();
		$keys = array_keys($_REQUEST);
		foreach($keys as $key){
			if(strpos($key,"arg_") === 0){
				$name = substr($key,4);
				$args[$name] = $_REQUEST[$key];
			}
	}
  
	$generator = Generator::loadByID($gen_id);
	$valid = true;
	$errors = null;
	if($step == 1){
	  $valid = $generator->validate($args, $errors); 
	}
  
  
 
  //gather list of arguments needed
  $arg_list = generator_arguments_get($gen_id);
  
  if((!$valid && $step == 1) || (($step == 0 || !isset($step)) AND isset($gen_id)) ) { //display form for input  


    $copy = array();
    if(isset($_REQUEST['from'])){  
      $copy_info = product_get($_REQUEST['from']);
      if(isset($copy_info['arguments'])){
	$copy = unserialize($copy_info['arguments']);
      }
    }
    
    ?>
   
   
		

   <table>
   <form method="post">
   <input type="hidden" name="step" value="1">
   <input type="hidden" name="id"	value="<?php echo $gen_id?>">
		<?php
		if(count($arg_list) > 0){
		  echo "<p>Please enter the required arguments for the matrix.</p>";
		   if(!$valid){
			echo "<h2><b>ERROR: $errors</b></h2>";
		   }  

			foreach($arg_list as $arg){
				$name = $arg['name'];
				$type = $arg['type'];
				$variable = $arg['variable'];
				$default = $arg['default_value'];

				

				if(count($args) > 0){ //use the values the user passed
				  $value =($type == "BOOLEAN")?array_key_exists($variable, $args):$args[$variable];
				}else if(isset($_REQUEST['from'])){               //pull the values from the database
				  $value = $copy[$variable];
				}else{ //user has not submitted and isn't copying
				  $value = $default;
				}

				$description = $arg['description'];
				$optional = $arg['optional'];
				$options = $arg['options'];
			
			
			
				if($type == "STRING" || $type == "INTEGER" || $type == "FLOAT" || $type == "DOUBLE"){
				?>
					<tr>
						<td><b><?php echo $name?>&nbsp;<?php echo($optional)?"":"*";?></b></td><td><input type="text"  value="<?php echo $value;?>" name="arg_<?php echo $variable?>"></td>
					</tr>
					<tr><td colspan="2"><em><?php echo "&nbsp;&nbsp;&nbsp;" .$description; ?></em></td></tr>

				<?php
				}elseif($type == "BOOLEAN"){
				?>
					<tr>
						<td><b><?php echo $name?>&nbsp;<?php echo($optional)?"":"*";?></b></td><td><input type="checkbox"  value="<?php echo ($value)?"true":"false";?>" name="arg_<?php echo $variable?>"></td>
					</tr>
					<tr><td colspan="2"><em><?php echo "&nbsp;&nbsp;&nbsp;" . $description; ?></em></td></tr>
				<?php
				}
				elseif($type == "SELECT"){
				?>
					<tr>
						<td><b><?php echo $name?>&nbsp;<?php echo($optional)?"":"*";?></b></td><td><select  value="<?php echo $value;?>" name="arg_<?php echo $variable?>">
						
						<?php
						foreach(unserialize($options) as $option){
					           if($option == $value){
						     echo "<option selected=\"selected\" value=\"$option\">$option</option>";
						   }else{
						     echo "<option value=\"$option\">$option</option>";
						   }
					        }
						?>
						
						</select></td>
					</tr>
					<tr><td colspan="2"><em><?php echo "&nbsp;&nbsp;&nbsp;" . $description; ?></em></td></tr>
				<?php
					 }
			}
		  echo  "<tr><td><br><br><br><br></td></tr>";
		}else{
			echo "<p> This generator has no inputs</p>";
		}
		?>
      <tr><td><b>Problem Description</b><br> (optional / 100 characters max) </td></tr>
      <tr><td><textarea name="problem_description"></textarea></td></tr>
		   </tr>
      
		<tr>
	    		   <td><input type="submit"  value="Submit for Generation"></td> 
		   </tr>
   </form>
   </table>
   <?php
		   }  elseif($step == 1 && $valid){

		?> 
		
		<p>Your request has been received. You will be notified by email when it is built.  </p>
		<?php
	        //$generator->build($args, "/tmp/testing/");
	
		    $description = $_REQUEST['problem_description'];
        
		    product_generate($_REQUEST['id'],$security->getUser()->getID(),$args, $description);
                emailer_send_job_received($security->getUser()->getId(),"");
  }else{
	?>
		<p>I'm sorry, I was unable to process that request. </p>
	<?php
  }
}
?>
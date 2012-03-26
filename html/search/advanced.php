<?php require_once("../includes/db/common.inc.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<script type="text/javascript" src="/js/jquery.js"></script>


<link  rel="stylesheet" type="text/css" href="/css/main.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Test Problem Server</title>
</head>
<body>
<div id="wrapper" style="">
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
		<?php include("./submenu.inc.php");?>
	</div>
	<div id="content" >
		<h1>Advanced Search</h1>
	<?php

	if(count($_GET) < 1){
	  $cols = db_query("describe anamod_data", true);
         
          echo "<form>";
          $groups;
	  foreach($cols as $col){
	    //attempt to determine the type of field
	    $type = "String"; //default
            $name = ucwords(str_replace('-', ' ', $col['Field']));
            $name_parts  = (explode(' ' ,$name));
            $group_name = $name_parts[0];
            $groups[$group_name][] = $col;
          }
	  
	  

          foreach($groups as $group_name=>$cols){

	    if($group_name == "File_id"){
	      continue;
	    }


	    echo '<div class="column" style="float:none;width:50em;max-width:50em;">';
           
             echo "<h2>$group_name</h2>";
	     foreach($cols as $col){
	       //attempt to determine the type of field
	      $type = "String"; //default
              $name = ucwords(str_replace('-', ' ', $col['Field']));
              $name_parts  = (explode(' ' ,$name, 2));
              $group = $name_parts[0];
              $name = $name_parts[1];
	      $field = $col['Field'];

	      if(stristr($col['Type'], "double")){
		 $type = "Double";
	      }
	    
	      if(stristr($col['Type'], "int")){
		 $type= "Integer";
	      }

	      echo '<p"><label for="' . $field . '">' . $name . '<em>&nbsp;(' . $type . ') </em></label></p>';

	     
	      echo '<p> <b>&nbsp;is&nbsp;</b> <select onchange="if(this.value.indexOf(\'<=>\') == 0){document.getElementById(\''.$field . '_compare' .'\').style.visibility=\'visible\'}else{document.getElementById(\''.$field . '_compare' .'\').style.visibility=\'hidden\'}" style="padding-left:.5em;padding-right:.5em;width:16em; "  type="text" name="'. $field. '_relation' .  '" id="'. $field. '"/></p>' ;
	      
	      echo '<option value="=">equal to </option>';
	      if($type == "Double" || $type == "Integer"){
		  echo '<option value="<=">less than or equal to </option>';
		  echo '<option value=">=">greater than or equal to </option>';
		  echo '<option value="<=>">between (inclusive)</option>';
	       }
	     
	      echo "</select>";
	      echo '<input  style="display:inline;margin-left:1em;margin-right:1em;" size="7" type="text" name="'. $field. '_1' . '" id="'. $field. '_1'. '"/>' ;
	      echo "<span id='" . $field . "_compare' style=\"visibility:hidden;\">";
		 echo "<b>AND</b>";
	         echo '<input style="" size="7"  type="text" name="'. $field. '_2' . '" id="'. $field. '_2' .  '"/></p>' ;
	      echo "</span>";
	      }
   
	    echo '</div>';
            }
          echo '<p><input style="float:right;clear:both;font-size:1em;" type="submit" value="Search"></p>';
	  echo "</form>";

	}else{
	  
	  $db_cols = db_query("describe anamod_data", true);
	  $cols = array();

	  foreach($db_cols as $db_col){

	    $cols[] = $db_col['Field'];
	  }
	  
	  

	  //build select statement
	  $query = "SELECT anamod_data.* , file.name as file_name, product.id as product_id , product.identifier as product_identifier, generator.name as generator_name , generator.id as generator_id, collection.name as collection_name, collection.collection_id as collection_id, file.id as file_id  FROM anamod_data LEFT JOIN file ON file.id = anamod_data.file_id LEFT JOIN product ON product.id = file.product_id  LEFT JOIN generator ON generator.id = product.generator_id  LEFT JOIN collection ON generator.collection_id = collection.collection_id WHERE ";
	  //needed for escape string
	  db_connect();

	  $first = true;
	  $columns_used = array();

	  foreach($cols as $column){
	    
	    
	    $value1 = $_GET[$column . "_1"];
	    $value2 = $_GET[$column . "_2"];
	    $relation = $_GET[$column . "_relation"];
	    
	    	    
	 
	    if(strlen($value1) > 0){
	      
	      //equal
	      if($relation == "="){
		if($first){
		  $first = false;
		}else{
		  $query .= " AND ";
		}

		
		$key =  mysql_real_escape_string($column);
		$value = mysql_real_escape_string($value1);
		$query .= " `$key` = '$value' ";
	      
		$columns_used[] = $key;
	      }

	      
	      //less than
	      if($relation == "<="){
		if($first){
		  $first = false;
		}else{
		  $query .= " AND ";
		}

		$key =  mysql_real_escape_string($column);
		$value = mysql_real_escape_string($value1);
		$query .= " `$key` <= '$value' ";
		

		$columns_used[] = $key;
	     
	      }
	      
	      
	      //greater than
	      if($relation == ">="){
		if($first){
		  $first = false;
		}else{
		  $query .= " AND ";
		  }
		$key =  mysql_real_escape_string($column);
		$value = mysql_real_escape_string($value1);
		$query .= " `$key` >= '$value' ";  
	     
		$columns_used[] = $key;
	     
	      }

	      //between
	      if($relation == "<=>"){
		if(strlen($value2) > 0){
		  if($first){
		    $first = false;
		  }else{
		    $query .= " AND ";
		  }
		  $key =  mysql_real_escape_string($column);
		  $value1 = mysql_real_escape_string($value1);
		  $value2 = mysql_real_escape_string($value2);
		  $query .= " `$key` BETWEEN '$value1' AND '$value2'";
		

		$columns_used[] = $key;
	     
		}
	      }
	    } 
	  }


	  $query = $query . " ORDER BY product.identifier, file_name";
	  //echo $query;


	  $results = db_query($query, true);
	  if(count($results) < 1){
	    echo "<h2><b>No results found</b></h2>";
	  }else{
	    echo "<h2>Matrices (" .count($results) ." results)</h2>";
	    
	    $index = 1;
	    
	    echo "<table>";
	    echo "<tr><th>#</th><th>Collection</th><th>Generator</th><th>Product ID</th><th>File Name</th>";
	    
	    //print out the headers for the relevant statistics 
	    foreach($columns_used as $col_header){
	      echo "<th>" . $col_header . "</th>";
	    }
	    echo "</tr>";

	    foreach($results as $result){
	      
	      echo "<tr>";
	      echo "<td>" . $index . "</td>";
	      echo "<td><a href=\"/matrices/collection.php?id=" . $result['collection_id'] . "\">". $result['collection_name'] .  "</a></td>";
	      echo "<td><a href=\"/matrices/generator.php?id=" . $result['generator_id'] . "\">". $result['generator_name'] .  "</a></td>";
	      echo "<td><a href=\"/matrices/matrix.php?id=" . $result['product_identifier'] . "\">". $result['product_identifier'] .  "</a></td>";
	     
	      echo "<td>" . $result['file_name'] . "</td>" ;
	      
	      foreach($columns_used as $col_name){
		echo "<th>" . $result[$col_name] . "</th>";
	      }

	      echo "</tr>";
	      $index++;
	    }


	    //close table
	    echo "</table>";
	    
	    
	    //var_dump($columns_used);
	    //echo "<hr>";
	    //var_dump($results);
	  }
	}

		?>
	</div>
	<div id="footer">
	</div>
</div>
</body>
</html>

<?php

require_once "../includes/db/data.inc.php";


$error = false;    //Whether or not an error has occured
$error_msg = "";   //Explaination of error
$folder;           //The temp directory that holds the file or files to be downloaded, should be deleted after use
$mode = "NONE";     //Conversion Mode


//$_GET['fileid'] = 58;
//$_GET['mode'] = "MM2PET";

/*
 /////////////////  Move data into a working directory for operation  ////////////////////
*/

if(isset($_GET['identifier'])){
  $identifier = $_GET['identifier']; //download identifier 
  if(preg_match("/[a-z0-9]{6}/i",$identifier)){
    
    $data_dir = "/data/storage/$identifier/";
    if(file_exists($data_dir)){

      //create a folder to hold the files
      $folder = tempnam  ("/data/downloads/","$identifier" . "_");
      unlink($folder);
      mkdir($folder);
      
      //copy data into folder
      $cmd = "cp -r $data_dir/* $folder";
      shell_exec($cmd);
     
    }else{
      $error = true;
      $error_msg = "That product does not exist";
    }
  }else{
    $error = true;
    $error_msg = "File ID's must be well formed";
  }
 }else if(isset($_GET['fileid'])){
  $file_id = $_GET['fileid'];
  $info = file_info($file_id);
  if($info != false){
    $identifier = $info['identifier'];
    $file_name = $info['name'];



    $source = "/data/storage/$identifier/public/$file_name";

    if(file_exists($source)){
      
      //create a folder to hold the files
      $folder = tempnam  ("/data/downloads/","$identifier". "_");
      unlink($folder);
      mkdir($folder);
      

      $cmd = "cp $source $folder";
      shell_exec($cmd);
      
    }else{
      $error = true;
      $error_msg = "That file appears to have been deleted";
    }
       
  }else{
    $error = true;
    $error_msg = "That file does not exist";
  }

 }else{
  $error = true;
  $error_msg = "You did not specific what to download. Please give a fileid or a product identifier";
 }







/*
 ///////////////////  Handle any file conversions  ///////////////////
*/

if(isset($_GET['mode'])){
  $mode = strtoupper($_GET['mode']);
}

if(!$error && ($mode == "NONE" || $mode == "MM2PET" || $mode == "PET2MM")){
  
  $files = scandir($folder);

  //Convert
  if($mode == "PET2MM"){



    //NOT IMPLEMENTED -- LOCATE CONVERTER
    
    
  }

  if($mode == "MM2PET"){
    $ext = ".mtx";
    foreach ($files as $file){
     
      if(substr( strtolower($file), strlen( $file ) - strlen( $ext ) ) === $ext){
	//convert
	
	//echo "Converting $file...\n";
	//chmod($folder, 775);
	$bare_name = substr($file, 0, strlen($file) - strlen($ext));
        //echo "Bare name is $bare_name\n";
	$cmd = "cd $folder; /opt/apps/utilities/mm_to_petsc/convertor -mm $bare_name";
	//$cmd = "/var/www/html/utilities/mm_2_petsc $folder/$file";
	//        echo "Command is $cmd\n";
	//	echo $cmd;
	 shell_exec($cmd);



	//if this is a single file download, replace the file and delete the info file  
	if(count($files) -2 == 1){
	  unlink($folder . "/" . $bare_name . ".mtx");
	  unlink($folder . "/" . $bare_name . ".ptc.info");
	}


      }
    } 
  }
}else if(!$error){
  $error = true;
  $error_msg = "Invalid Conversion Mode";
}
  

//echo "Exiting...";
//exit(0);
	 



/*
 ///////////////////  Compress if Needed  ///////////////////
*/


if(!$error && (count(scandir($folder)) > 3)){ //explicit or multiple files


  //create informational readme 
  $readme = fopen("$folder/README.txt","w");
  $readme_msg = "This matrix was produced by the Test Problem Server at http://lovett.tacc.utexas.edu/. \r\n \r\n";
  $readme_msg .= "Matrix ID:".  $identifier ."\r\n";
  $readme_msg .= "\r\n";
  
  if($mode == "PET2MM"){
    $readme_msg .= "PETSc files were automatically converted into MatrixMarket format. \r\n";
  }
  
  if($mode == "MM2PET"){
    $readme_msg .= "MatrixMarket files were automatically converted into PETSc format. \r\n";
  }
  
  fwrite($readme, $readme_msg);
  fclose($readme);


  //compress 
  $target = $folder . ".tar.gz";
  $name_parts = explode("/", $folder);
  $dir = $name_parts[count($name_parts) - 1];
  $cmd = "cd /data/downloads/ ; tar -czvf $target $dir";
  shell_exec($cmd);

  
  //permissions magic
  chgrp($folder,"G-800756");
  chmod($folder,0777);
  chgrp($target,"G-800756");
  chmod($target,0777);
  

  
 }else if(!$error) {
  $files = scandir($folder);
  if(count($files) > 2){
    $target = $folder . "/" . $files[2];
  }else{
    $error = true;
    $error_msg = "File conversion failed";
  }
 }




/*
 ///////////////////  Output File  ///////////////////
*/


if(!$error){
  //filename that the user gets as default
  
  $name_parts = explode("/", $target);
  $download_name = $name_parts[count($name_parts) - 1];
  $local_file = $target;
 
  // send headers
  header('Cache-control: private');
  header('Content-Type: application/octet-stream'); 
  header('Content-Length: '.filesize($local_file));
  header('Content-Disposition: filename='.$download_name);
  
  // flush content
  flush();
  
  // open file stream
  $file = fopen($local_file, "rb");
  
  // send the file to the browser
  print fread ($file, filesize($local_file)); 
  
  // close file stream
  fclose($file);
}



/*
 ///////////////////  Tidy Up  ///////////////////
*/
if(isset($folder)){
  unlink ($folder);
  if(!$error){
    exit(0);
  }
 }



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Texas Problem Server - Retrieve File</title>
<head>
<link  rel="stylesheet" type="text/css" href="/css/main.css" /><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
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
		<?php
		if(!$error){
		?>
			<h1>Please enter file name/id below to retrieve your file </h1>
			<form method="get">
			<p>Name/ID: <input name="id" type="text"></p>
			<p><input value="Download" type="submit"></p>
			</form>
		<?php
		}else{
		?> 
			<h1>An error occured while retrieving your file.</h1>
			<p><?php echo $error_msg; ?> </p>
		<?php
		}
		?>
	</div>
	<div id="footer">
	</div>
</div>
</body>
</html>

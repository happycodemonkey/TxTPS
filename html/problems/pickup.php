<?php

require_once "../includes/db/data.inc.php";

if(isset($_GET['id'])){
	
	// local file that should be send to the client
	$id = $_GET['id'];
	if(preg_match("/[a-z0-9]{6}/i",$id)){
		
		$data_dir = "/data/storage/$id/";
		if(file_exists($data_dir)){
			//compress files into an archive
			$folder = "/data/downloads/$id";
			$target = $folder . ".tar.gz";
			if(!file_exists($folder)){
				mkdir($folder);
				
				//copy data into folder
				$cmd = "cp -r $data_dir/* $folder";
				shell_exec($cmd);
				
				//create informational readme 
				$readme = fopen("$folder/README.txt","w");
				$readme_msg = "This matrix was produced by the Test Problem Server at http://lovett.tacc.utexas.edu/. \r\n \r\n";
				$readme_msg .= "Matrix ID:".  $_GET['id'] ."\r\n";
				fwrite($readme, $readme_msg);
				fclose($readme);
				
				//create tar.gz of that folder
				$cmd = "cd /data/downloads/ ; tar -czvf $target $id";
				shell_exec($cmd);
				//echo $cmd;
			}
			chgrp($folder,"G-800756");
			chmod($folder,0777);
			chgrp($target,"G-800756");
			chmod($target,0777);
			// filename that the user gets as default
			$download_name = $id . ".tar.gz";
			$local_file = $target;
			/*
			 Note: for some reason is_file reports false for the generated tarball. This should eventually find a way to check that it was actually created. 
			*/
			if(true) { 
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
				exit(); 
			}else {
				$error = true;
				$error_msg = "Was unable to compress files for download";
			}
		}else{
			$error = true;
			$error_msg = "There are no files with that ID.";
		}
	}else{
		$error = true;
		$error_msg = "File ID's must be well formed";
	}
}elseif(isset($_GET['fileid'])){
  	// local file that should be send to the client
	$file_id = $_GET['fileid'];
	$info = file_info($file_id);
	if($info != false){ 
		
	
	        $product_id = $info['identifier'];
		$file = $info['name']; 

	        $source = "/data/storage/$product_id/$file";
		
		//echo $source;

		if(file_exists($source)){
			
			// filename that the user gets as default
			$download_name = $info['name'];
		
			if(true) { 
				// send headers
				header('Cache-control: private');
				header('Content-Type: application/octet-stream'); 
				header('Content-Length: '.filesize($source));
				header('Content-Disposition: filename='.$download_name);
			 
				// flush content
				flush();
			 
				// open file stream
				$file = fopen($source, "rb");
			 
				// send the file to the browser
				print fread ($file, filesize($source)); 
			 
				// close file stream
				fclose($file);
				exit(); 
			}else {
				$error = true;
				$error_msg = "Was unable to open files for download";
			}
		}else{
			$error = true;
			$error_msg = "There are no files with that ID.";
		}
	}else{
		$error = true;
		$error_msg = "There is no such file";
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
			<h1>An error occured while retrieving your file.<h1>
			<p><?php echo $error_msg; ?> </p>
			<p><a href="pickup.php">Go back</a></p>
		<?php
		}
		?>
	</div>
	<div id="footer">
	</div>
</div>
</body>
</html>

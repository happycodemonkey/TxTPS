<?php

require_once "./includes/db/data.inc.php";


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
    
    $data_dir = "/data/storage/$identifier/public/";
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
  $file_query = "SELECT * FROM file WHERE id=%d";
  $file_query = sprintf($file_query, $file_id);
  $info       = false;
  $result     = db_query($file_query, true);
  if(count($result) > 0){
    $info = $result[0];
  }
  if($info != false){
    $problem_query = "SELECT * FROM product WHERE id=%d";
    $problem_query = sprintf($problem_query, $info['problem_id']);
    $result        = db_query($problem_query,true);
    $result        = $result[0];
    $identifier    = $result['identifier'];
    $file_name     = $info['name'];



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
      $error_msg = "That file ($source) appears to have been deleted";
    }
       
  }else{
    $error = true;
    $error_msg = "That file does not exist in the database.";
  }

 }else{
  $error = true;
  $error_msg = "You did not specific what to download. Please give a fileid or a product identifier";
 }



/*
 ///////////////////  Compress if Needed  ///////////////////
*/


if(!$error && (count(scandir($folder)) > 3)){ //explicit or multiple files


  //create informational readme 
  $readme = fopen("$folder/README.txt","w");
  $readme_msg = "This matrix was produced by the Test Problem Server at http://lovett.tacc.utexas.edu/. \r\n \r\n";
  $readme_msg .= "Matrix ID:".  $identifier ."\r\n";
  $readme_msg .= "\r\n";
  
  
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

echo $error_msg;
?>
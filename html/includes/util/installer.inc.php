<?php 
require_once(dirname(__FILE__) . "/../db/generators.inc.php");

function install_collection($install_id,$script,&$errors){ 
	
	//move the upload folder, and give it reasonable permissions
	$tmp_dir = "/data/uploads/install_" . $install_id . "/";
	$install_dir = "/data/install/". $install_id . "/";
	$moved = rename($tmp_dir, $install_dir);
	chmod($install_dir, 0775); 
	chgrp($install_dir,"G-800756");
	
	
	//create a data dir, allow loose permissions on it
	$data_dir = $install_dir . "data/";
	mkdir($data_dir);
	chmod($data_dir, 0777);
	chgrp($data_dir,"G-800756");
	
	
	//parse manifest file for basic info
	$manifest = $install_dir . $script;
	$xml = simplexml_load_file($manifest);
	$name = $xml->structure->title ;
	$format = $xml->structure->format;
	

	//record the installation in the database
	$collection_id = generator_collection_add($name, $install_id ,"No Description", $format);
	
	echo $name . "\n<br/>";
	echo $install_id . "\n<br/>";
	echo $format . "\n<br/>";
	echo "Collection ID is: $collection_id";
	
	//give each file the permission to execuute and be read
	shell_exec("cd $install_dir; chmod a+rx *");
	

	//if the collection was inserted, add the information for each generator
	if($collection_id !== false){

		//execute commands for the collection if any
		foreach($xml->command as $command){
			echo "<p><b>" . $command . "</b></p>";
			echo "<p>". passthru("bash ; export PATH=/opt/:/opt/intel/compiler9.1/cc/bin/:\$PATH ; cd $install_dir ;  module load TACC; export; $command 2>&1 ; export") . "</p> 
\n"; 
		}
		foreach ($xml->generator as $generator) {
			$gname  = $generator->name;
			$gscript= $generator->script;
			$gtype= $generator->type;
			$gdesc= "None";
			//TODO gather type information
			$gtype_id = 0;
					
			//run each command present for the generator in order
			foreach($generator->command as $command){ 
				echo "<p><b>" . $command . "</b></p>";
				echo "<p>" . $install_dir . "</p>";
				echo "<p>". passthru("bash ; export PETSC_ARCH=em64t 2>&1; export PETSC_DIR=/opt/apps/petsc/dev/em64t
 2>&1 ; cd $install_dir ;  PATH=/opt/:/opt/intel/compiler9.1/cc/bin/:\$PATH  ;  module load TACC; export;  $command 2>&1 ;") . "</p>";
			} 
			
			
			//set the script to have loose permissions, this should be changed in the future 
			//TODO : set permissions to be more strict for the scripts. Maybe 755
			chmod($install_dir . $gscript , 0777);
			
			//install 
			//echo "Installing: $gname";
			$gen_id = generator_add($gname , $gscript, $gdesc, $gtype_id, $collection_id);
			//insert into the database
			$args = array();
			$count = 0;
			foreach ($generator->argument as $arg){
				$args[$count] = array();
				$args[$count]['name'] = $arg->title;
				$args[$count]['variable'] = $arg->name;
				$args[$count]['type'] = "string";//$arg['type'];
				$count++;
			}
			generator_arguments_set($gen_id, $args);
		}
	}else{
		$error= "The collection could not be added to the database.";
		return false;
	}
	return true;
}

?>

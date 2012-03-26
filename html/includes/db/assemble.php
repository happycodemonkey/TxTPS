<?php
include_once("./includes/db/generators.inc.php");
include_once("./includes/db/data.inc.php");


do{
	//sleep for five seconds, as to not tank the server
	$queue = product_queue();
	foreach($queue as $request){
		$identifier = "";
		echo "Request<br/>"
		var_dump($request);
		echo "\n\n<br>";
		switch(strtoupper($request['type'])){
			case "MATLAB":
			$identifier = MATLAB_handler($request);
			break;
			case "NATIVE":
			case "SOURCE":
			$identifier = NATIVE_handler($request);
			break;
		}
	

	//send notification email 
	$name = "Test Problem Server"; //senders name
	$email = "tps@lovett.tacc.utexas.edu"; //senders e-mail adress
	$recipient = "kneeland@tacc.utexas.edu"; //recipient
	$mail_body = "Your matrix has been generated. You can pick it up via the following url: \r\n \r\n http://lovett.tacc.utexas.edu/beta/pickup.php?id=" . $identifier ; //mail body
	$subject = "TPS: Matrix Generation Complete"; //subject
	$header = "From: ". $name . " <" . $email . ">\r\n"; //optional headerfields

	mail($recipient, $subject, $mail_body, $header);	//send message notifying them of the installation
	
	//store result 
	product_store($request['id'],$identifier);
	}
}while(false);


function MATLAB_handler($request){
	$base_url = "/data/install";
	$dir_url = $base_url . "/" . $request['identifier'] . "/" ;
	$data_url = $dir_url . "data/";

	$store_url = tmpdir("/data/storage/", "");	
	$parts = (split("/",$store_url));
	$ident = $parts[count($parts) - 1];
	//$dir_url = "/var/www/html/beta/nlevp/private/";
	$script =  substr($request['script'],0,strpos($request['script'],"."));;
	$args = implode(",",unserialize($request['arguments']));
	$shell_command = "/opt/matlab/bin/matlab -r  -nosplash -r \"cd $dir_url , $script($args) , exit\"  2>&1";
	echo $shell_command . "\n";
	shell_exec($shell_command);

	//grab the file in data
	$copy_command = "cp $data_url/* $store_url";
	shell_exec($copy_command);
	$cleanup_command = "rm -fr $data_url/*";
	shell_exec($copy_command);
	$beautify_command = "chmod a+rx $store_url; cd $store_url; chmod a+rx *";
	shell_exec($beautify_command);
	return $ident;
}


function NATIVE_handler($request){
	$base_url = "/data/install";
	$dir_url = $base_url . "/" . $request['identifier'] . "/" ;
	$data_url = $dir_url . "data/";

	$store_url = tmpdir("/data/storage/", "");	
	$parts = (split("/",$store_url));
	$ident = $parts[count($parts) - 1];
	
	var_dump($parts);

	$script =  substr($request['script'],0,strpos($request['script'],"."));;
	var_dump($script);
	
	$args = unserialize($request['arguments']);
	$arg_list = "";
	foreach($args as $name=>$arg){
		$arg_list .=" -$name $arg";
	}
	$shell_command = "$dir_url/$script " . $arg_list ." > ./data/output.txt";
	echo $shell_command . "\n";
	shell_exec($shell_command);

	//grab the file in data
	$copy_command = "cp $data_url/* $store_url";
	shell_exec($copy_command);
	$cleanup_command = "rm -fr $data_url/*";
	shell_exec($copy_command);
	
	return $ident;
}

function tmpdir($path, $prefix)
{
        // Use PHP's tmpfile function to create a temporary
        // directory name. Delete the file and keep the name.
        $tempname = tempnam($path,$prefix);
        if (!$tempname)
                return false;

        if (!unlink($tempname))
                return false;

        // Create the temporary directory and returns its name.
        if (mkdir($tempname))
                return $tempname;

        return false;
}
?>

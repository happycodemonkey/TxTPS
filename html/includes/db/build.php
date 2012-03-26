
<?php
require_once("./includes/util/security.inc.php");
require_once("./includes/util/emailer.inc.php");
include_once("./includes/db/generators.inc.php");
include_once("./includes/db/data.inc.php");

/* Require the user to be logged in */
if(!$auth->is_logged_in()){
	$auth->forward("login.php");
}



$action = $_REQUEST['action'];
$step = $_REQUEST['step'];
$gen_id = $_REQUEST['id'];

$subtitle = "Generate Matrix";

$gen_info = generator_details_get($gen_id);
$title = $gen_info["name"];
$description = $gen_info['description'];

function build(){
  global $auth;
  global $step;
  global $gen_id;

  
  
 
  //gather list of arguments needed
  //$arg_list = generator_arguments_get($gen_id);
  var_dump($arg_list);  

  if(($step == 0 || !isset($step)) AND isset($gen_id)) { //display form for input   ?>
 		<h2>Description</h2>
		<p><?php echo $description; ?></p>
		
		<h2>Variables</h2>
 
   <table>
   <form method="post">
   <input type="hidden" name="step" value="1">
   <input type="hidden" name="id"	value="<?php echo $gen_id?>">
		<?php
		if(count($arg_list) > 0){
			echo "<p>Please enter the required arguments for the matrix.</p>";
			foreach($arg_list as $arg){
				if($arg['type'] == "FLOAT" || $arg['type'] == "INTEGER" || $arg['type'] == "DOUBLE"){
				?>
					<tr>
						<td><?php echo $arg['name']?></td><td><input type="text"  value="" name="arg_<?php echo $arg['variable']?>"></td>
					</tr>
				<?php
				}elseif($arg['type'] == "BOOLEAN"){
				?>
					<tr>
						<td><?php echo $arg['name']?></td><td><input type="checkbox"  value="" name="arg_<?php echo $arg['variable']?>"></td>
					</tr>
				<?php
				}
			}
		}else{
			echo "<p> This generator has no inputs</p>";
		}
		?>
		<tr>
			<td><input type="submit"  value="Submit for Generation"></td> 
		</tr>
   </form>
   </table>
   <?php
  }  elseif($step == 1){
		?> 
		<p>Your request has been processed. Maybe.</p>
		<?php
		$args = array();
		$keys = array_keys($_REQUEST);
		foreach($keys as $key){
			if(strpos($key,"arg_") === 0){
				$name = substr($key,5);
				$args[$name] = $_REQUEST[$key];
			}
		}
		product_generate($_REQUEST['id'],$auth->get_user_id(),$args);

		
		emailer_send_job_received($auth->get_user_id(),"");
		echo "<p>Additionally, an email has been sent to your address.</p>";
		
			//assemble the matrix too while we're here
			//$result = shell_exec("php assemble.php");
			echo $result;
		echo "<p> Matrix was built";
  }else{
	?>
		<p>I'm sorry, I was unable to process that request. </p>
	<?php
  }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Test Problem Server</title>
<head>
<link  rel="stylesheet" type="text/css" href="./css/main.css" />
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
		<?php include("./includes/templating/title.inc.php"); ?>
	</div>

	<div id="menu">
		<?php include("./includes/templating/menu.inc.php"); ?>
	</div>
	<div id="sidemenu">
		<ul>
			<li><a class="sidemenulink" href="#">Accounts</a>
			<li><a class="sidemenulink" href="#">Support</a>
			<li><a class="sidemenulink" href="#">Contact</a>
		</ul>
	</div>
	<div id="content">
        <h1><?php echo $subtitle . " : " . $title; ?></h1>
        <?php
			build();
			
		
		?>
	</div>
	<div id="login">
		<?php require("./includes/templating/login.inc.php"); ?>
	</div>
	<div id="footer">
	</div>
</div>
</body>
</html>

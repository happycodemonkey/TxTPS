<?php

//include common header
require_once("includes/common_code.php");

$smarty->assign("generator_id",$_GET['id']);
$smarty->display('tps_generator.tpl');

?>

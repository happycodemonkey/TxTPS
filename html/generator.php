<?php

//include common header
require_once(getEnv("DOCUMENT_ROOT") . "/includes/common_code.php");

$smarty->assign("generator_id",$_GET['id']);
$smarty->display('tps_generator.tpl');

?>

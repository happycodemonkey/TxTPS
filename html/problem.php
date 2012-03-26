<?php

//include common header
require_once("includes/common_code.php");

$smarty->assign("problem_id",$_GET['id']);
$smarty->display('tps_problem.tpl');

?>

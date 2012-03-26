<?php

//include common header
require_once("includes/common_code.php");

$smarty->assign('user', $user);
$smarty->display('tps_404.tpl');



?>

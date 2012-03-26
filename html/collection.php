<?php
//include common header
require_once("includes/common_code.php");

$smarty->assign("collection_id",$_GET['id']);
$smarty->display('tps_collection.tpl');
?>

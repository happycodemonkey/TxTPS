<?php
//include common header
require_once(getEnv("DOCUMENT_ROOT") . "/includes/common_code.php");

//get news
require_once(getEnv("DOCUMENT_ROOT") . "/includes/db/common.inc.php");
$query = "SELECT * FROM news ORDER BY timestamp DESC";
$stories = db_query($query,true);
$featured_story = $stories[0];


$smarty->assign('featured_story', $featured_story);
$smarty->display('tps_index.tpl');



?>

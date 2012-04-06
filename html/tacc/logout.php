<?php
require_once(getEnv("DOCUMENT_ROOT") . "/includes/classes/Security.class.php");
$security = Security::getInstance();
$security->logout();
?>

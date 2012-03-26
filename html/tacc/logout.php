<?php
require_once("/var/www/html/includes/classes/Security.class.php");
$security = Security::getInstance();
$security->logout();
?>
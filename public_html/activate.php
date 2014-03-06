<?php

define("WEBPAGE_CONTEXT", "activate.php");
require_once("../resources/global.inc.php");

if (isset($_GET['hash'])) {
    $activation_hash = $_GET['hash'];
    $query = "UPDATE `users` SET `active`=1 WHERE `activate_hash`='".$db->real_escape_string($activation_hash)."'";
    $result = $db->query($query);
    
    if ($result) {
	$message = "Your account has been activated!";
	header("Location: ".SITE_ROOT."/login.php?message=".urlencode($message));
	exit;
    }
}

header("Location: ".SITE_ROOT."/login.php");
exit;

?>
<?php

define("WEBPAGE_CONTEXT", "activate.php");

set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__,
    __DIR__."/../resources"
)));

require_once("global.inc.php");

if (isset($_GET['hash'])) {
    $activation_hash = $_GET['hash'];
    $query = "UPDATE `users` SET `active`=1 WHERE `activate_hash`='".$db->real_escape_string($activation_hash)."'";
    $result = $db->query($query);
    
    if ($result) {
	$_SESSION['notices'][] = "Your account has been activated!";
	header("Location: ".SITE_ROOT."/login.php");
	exit;
    }
}

header("Location: ".SITE_ROOT."/login.php");
exit;

?>
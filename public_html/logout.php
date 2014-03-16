<?php

define("WEBPAGE_CONTEXT", "index.php");

set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__,
    "../resources"
)));

require_once("global.inc.php");

if (isset($_SESSION['user'])) {
    foreach ($_SESSION['user'] as &$info) {
	unset($info);
    }
    unset($_SESSION['user']);
}
$_SESSION = array();
unset($_SESSION);
session_destroy();
session_start();

if (!is_logged_in()) {
    $_SESSION['notices'][] = "Successfully logged out.";
    header("Location: ".SITE_ROOT."/login.php");
    exit;
} else {
    $ERRORS[] = "Failed to log out.";
}

require_once("header.inc.php");

require_once("footer.inc.php");

?>
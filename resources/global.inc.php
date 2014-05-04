<?php

if (!defined("WEBPAGE_CONTEXT")) {
    exit;
}

require_once(__DIR__."/connect-db.inc.php");
require_once(__DIR__."/functions.inc.php");

session_start();
if (defined("REQUIRES_LOGIN") && REQUIRES_LOGIN) {
    if (!is_logged_in()) {
	$_SESSION['notices'][] = "Please log in to continue.";
	header("Location: ".SITE_ROOT."/login.php?redirect=".urlencode($_SERVER['REQUEST_URI']));
    }
}

$JS_FILES = array("js/favorite.js");
$CSS_FILES = array("css/styles.css");

$DEFAULT_PAGE_TITLE = "Twitter Clone";
$ERRORS = isset($_SESSION['errors']) ? $_SESSION['errors'] : array();
$NOTICES = isset($_SESSION['notices']) ? $_SESSION['notices'] : array();
$IMAGE_FORMATS = array('image/png', 'image/jpeg');

unset($_SESSION['errors']);
unset($_SESSION['notices']);

?>

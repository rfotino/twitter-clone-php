<?php

if (!defined("WEBPAGE_CONTEXT")) {
    exit;
}

require_once(dirname(__FILE__)."/connect-db.inc.php");
require_once(dirname(__FILE__)."/functions.inc.php");

$JS_FILES = array();
$CSS_FILES = array("styles.css");
$DEFAULT_PAGE_TITLE = "Twitter Clone";

?>
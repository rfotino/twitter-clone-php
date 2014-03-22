<?php

define("WEBPAGE_CONTEXT", "ajax");

set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__,
    __DIR__."/../../resources"
)));

require_once("global.inc.php");

if (isset($_GET['handle']) && $_GET['handle'] && (!isset($_SESSION['user']['handle']) || $_GET['handle'] != $_SESSION['user']['handle'])) {
    $handle = trim($_GET['handle']);
    $query = "SELECT * FROM `users` WHERE `handle`='".$db->real_escape_string($handle)."'";
    $result = $db->query($query);
    if ($result && $result->fetch_assoc()) {
	echo "This handle has been taken.";
    } else if (strlen($handle) > 0 && strlen($handle) <= 20) {
	echo "This handle is available!";
    }
}
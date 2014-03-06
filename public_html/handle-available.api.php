<?php

define("WEBPAGE_CONTEXT", "ajax");
require_once("../resources/global.inc.php");

if (isset($_GET['handle'])) {
    $handle = trim($_GET['handle']);
    $query = "SELECT * FROM `users` WHERE `handle`='".$db->real_escape_string($handle)."'";
    $result = $db->query($query);
    if ($result && $result->fetch_assoc()) {
	echo "This handle has been taken.";
    } else if (strlen($handle) > 0 && strlen($handle) <= 20) {
	echo "This handle is available!";
    }
}
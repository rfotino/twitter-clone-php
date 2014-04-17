<?php

if (!defined("WEBPAGE_CONTEXT")) {
    exit;
}

require_once(__DIR__."/config.inc.php");
$db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL. ".mysqli_connect_error();
}

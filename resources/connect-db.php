<?php

require_once(dirname(__FILE__)."/config.php");
$db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL. ".mysqli_connect_error();
}

?>
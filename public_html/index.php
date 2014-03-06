<?php

define("WEBPAGE_CONTEXT", "index.php");

require_once("../resources/global.inc.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

?>

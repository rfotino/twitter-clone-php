<?php

define("WEBPAGE_CONTEXT", "ajax");

set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__,
    __DIR__."/../../resources"
)));

require_once("global.inc.php");

if (!is_logged_in()) {
    exit;
}

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($post_id) {
    $query = "UPDATE `posts`
              SET `active`=0, `date_deactivated`=NOW()
              WHERE `post_id`=".$db->real_escape_string($post_id)."
              AND `user_id`=".$db->real_escape_string($_SESSION['user']['id']);
    $results = $db->query($query);
    if ($results) {
        echo json_encode(true);
        exit;
    }
}

echo json_encode(false);

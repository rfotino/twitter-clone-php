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

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($user_id) {
    if (is_following($user_id)) {
        $query = "UPDATE `follows`
                  SET `active`=0, `date_deactivated`=NOW()
                  WHERE `user_source_id`=".$db->real_escape_string($_SESSION['user']['id'])."
                  AND `user_destination_id`=".$db->real_escape_string($user_id);
        $results = $db->query($query);
        if ($results) {
            echo "Follow";
        }
    } else {
        $query = "INSERT INTO `follows`
                  (`user_source_id`, `user_destination_id`)
                  VALUES
                  (".$db->real_escape_string($_SESSION['user']['id']).",
                  ".$db->real_escape_string($user_id).")";
        $results = $db->query($query);
        if ($results) {
            echo "Unfollow";
        }
    }
}

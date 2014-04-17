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
    if (is_favorited($post_id)) {
        $query = "UPDATE `favorites`
                  SET `active`=0, `date_deactivated`=NOW()
                  WHERE `user_id`=".$db->real_escape_string($_SESSION['user']['id'])."
                  AND `post_id`=".$db->real_escape_string($post_id);
        $results = $db->query($query);
        if ($results) {
            echo json_encode(array(
                "success" => "Successfully unfavorited that post.",
                "favorite" => 0,
                "num_favorites" => get_num_favorites($post_id)
                ));
        } else {
            echo json_encode(array("error" => "Couldn't unfavorite that post at the moment."));
        }
    } else {
        $query = "INSERT INTO `favorites`
                  (`user_id`, `post_id`)
                  VALUES
                  (".$db->real_escape_string($_SESSION['user']['id']).",
                  ".$db->real_escape_string($post_id).")";
        $results = $db->query($query);
        if ($results) {
            echo json_encode(array(
                "success" => "Successfully favorited that post.",
                "favorite" => 1,
                "num_favorites" => get_num_favorites($post_id)
                ));
        } else {
            echo json_encode(array("error" => "Couldn't favorite that post at the moment."));
        }
    }
} else {
    echo json_encode(array("error" => "No post to favorite!"));
}

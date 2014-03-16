<?php

if (!defined("WEBPAGE_CONTEXT")) {
    exit;
}

function logout() {
    if (isset($_SESSION['user'])) {
        foreach ($_SESSION['user'] as &$info) {
            unset($info);
        }
        unset($_SESSION['user']);
    }

    $_SESSION = array();
    unset($_SESSION);
    session_destroy();
    session_start();
}

function get_user_by_handle($handle) {
    global $db;
    $query = "SELECT * FROM `users` WHERE `handle`='".$db->real_escape_string($handle)."'";
    $result = $db->query($query);
    if ($result) {
	return $result->fetch_assoc();
    }
    return null;
}
function get_user_by_email($email) {
    global $db;
    $query = "SELECT * FROM `users` WHERE `email`='".$db->real_escape_string($email)."'";
    $result = $db->query($query);
    if ($result) {
	return $result->fetch_assoc();
    }
    return null;
}
function get_user_by_id($user_id) {
    global $db;
    $query = "SELECT * FROM `users` WHERE `user_id`=".$db->real_escape_string((int)$user_id);
    $result = $db->query($query);
    if ($result) {
	return $result->fetch_assoc();
    }
    return null;
}
function get_this_user() {
    if (!is_logged_in()) {
	return null;
    }
    $user_id = $_SESSION['user']['id'];
    return get_user_by_id($user_id);
}

function display_errors() {
    global $ERRORS;
    
    echo "<div class=\"errors box\">\n";
    foreach ($ERRORS as $e) {
	echo "<div>$e</div>\n";
    }
    echo "</div>\n";
}
function display_notices() {
    global $NOTICES;
    
    echo "<div class=\"notices box\">\n";
    foreach ($NOTICES as $n) {
	echo "<div>$n</div>\n";
    }
    echo "</div>\n";
}

function display_user($user_id) {
    $user = get_user_by_id($user_id);
    if ($user) {
        $profile_link = SITE_ROOT.DIRECTORY_SEPARATOR."view-profile.php?id=".$user['user_id'];
        if (!$user['bio']) {
            $user['bio'] = "<em>empty bio</em>";
        } else if (strlen($user['bio']) > 100) {
            $user['bio'] = substr($user['bio'], 0, 100)." ...";
        }
        echo "<div class=\"display-user\">\n";
        echo "\t<div class=\"display-user-header\">\n";
        echo "\t\t<div class=\"display-name\"><a href=\"$profile_link\">{$user['name']}</a></div>\n";
        echo "\t\t<div class=\"display-handle\"><a href=\"$profile_link\">@{$user['handle']}</a></div>\n";
        echo "\t</div>\n";
        echo "\t<div class=\"display-user-content\">\n";
        echo "\t\t<div class=\"display-bio\">{$user['bio']}</div>\n";
        echo "\t</div>\n";
        echo "</div>\n";
    } else {
        return "";
    }
}

function is_logged_in() {
    return isset($_SESSION['user']['id']) && $_SESSION['user']['id'];
}

?>
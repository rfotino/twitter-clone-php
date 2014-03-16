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
    $query = "SELECT * FROM `users` WHERE `user_id`=".$db->real_escape_string($user_id);
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

function is_logged_in() {
    return isset($_SESSION['user']['id']) && $_SESSION['user']['id'];
}

?>
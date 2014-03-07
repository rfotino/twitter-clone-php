<?php

if (!defined("WEBPAGE_CONTEXT")) {
    exit;
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

function display_errors() {
    global $ERRORS;
    
    echo "<div class=\"errors\">\n";
    foreach ($ERRORS as $e) {
	echo "<p>$e</p>\n";
    }
    echo "</div>\n";
}
function display_notices() {
    global $NOTICES;
    
    echo "<div class=\"notices\">\n";
    foreach ($NOTICES as $n) {
	echo "<p>$n</p>\n";
    }
    echo "</div>\n";
}

function is_logged_in() {
    return isset($_SESSION['user']['id']) && $_SESSION['user']['id'];
}

?>
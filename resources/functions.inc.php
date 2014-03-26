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

function display_follow_button($user_id) {
    $follow_link = "javascript:followUser($user_id, this);";
    $button_text = is_following($user_id) ? "Unfollow" : "Follow";
    echo "<div class=\"align-right\">\n";
    echo "\t<span class=\"button\">\n";
    echo "\t\t<a onclick=\"".$follow_link."\">$button_text</a>\n";
    echo "\t</span>\n";
    echo "</div>\n";
}

function display_edit_profile_button() {
    echo "<div class=\"align-right\">\n";
    echo "\t<span class=\"button\">\n";
    echo "\t\t<a href=\"".SITE_ROOT."/edit-profile.php\">Edit</a>\n";
    echo "\t</span>\n";
    echo "</div>\n";
}

function display_create_post_form() {
    ?>
    <div class="form-wrapper box">
        <form id="create-post-form" name="create-post-form" method="post">
            <div class="input-wrapper">
                <label for="create-post-content" class="input-required">Compose new post</label>
                <textarea class="text-input" id="create-post-content" name="create-post-content" maxlength="<?php echo POST_MAX_LENGTH; ?>"
                          ><?php if (isset($_POST['create-post-content'])) { echo $_POST['create-post-content']; } ?></textarea>
                <script type="text/javascript">
                $(document).ready(function() {
                    $('#create-post-content').autosize();
                });
                </script>
            </div>
            <div class="submit-wrapper">
                <input class="submit-button" type="submit" name="create-post-submitted" value="Post" />
            </div>
        </form>
    </div>
    <?php
}

function is_logged_in() {
    return isset($_SESSION['user']['id']) && $_SESSION['user']['id'];
}

function is_following($user_id) {
    global $db;
    
    if (!is_logged_in()) {
        return false;
    }
    
    $query = "SElECT `follow_id`
              FROM `follows` 
              WHERE `user_source_id`=".$db->real_escape_string($_SESSION['user']['id'])."
              AND `user_destination_id`=".((int)$user_id)."
              AND `active`=1";
    $results = $db->query($query);
    return ($results && $results->num_rows);
}

?>
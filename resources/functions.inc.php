<?php

if (!defined("WEBPAGE_CONTEXT")) {
    exit;
}

function logout()
{
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

function get_user_by_handle($handle)
{
    global $db;
    $query = "SELECT * FROM `users` WHERE `handle`='" . $db->real_escape_string($handle) . "'";
    $result = $db->query($query);
    if ($result) {
        return $result->fetch_assoc();
    }
    return null;
}

function get_user_by_email($email)
{
    global $db;
    $query = "SELECT * FROM `users` WHERE `email`='" . $db->real_escape_string($email) . "'";
    $result = $db->query($query);
    if ($result) {
        return $result->fetch_assoc();
    }
    return null;
}

function get_user_by_id($user_id)
{
    global $db;
    $query = "SELECT * FROM `users` WHERE `user_id`=" . $db->real_escape_string((int)$user_id);
    $result = $db->query($query);
    if ($result) {
        return $result->fetch_assoc();
    }
    return null;
}

function get_this_user()
{
    if (!is_logged_in()) {
        return null;
    }
    $user_id = $_SESSION['user']['id'];
    return get_user_by_id($user_id);
}

function get_num_posts($user_id)
{
    global $db;
    $query = "SELECT COUNT(*) as `num_posts` FROM `posts`
              WHERE `user_id`=" . $db->real_escape_string((int)$user_id) . " AND `active`=1";
    $results = $db->query($query);
    if ($results) {
        $row = $results->fetch_assoc();
        if ($row && isset($row['num_posts'])) {
            return (int)$row['num_posts'];
        }
    }
    return 0;
}

function get_num_following($user_id)
{
    global $db;
    $query = "SELECT COUNT(*) as `num_following` FROM `follows`
              WHERE `user_source_id`=" . $db->real_escape_string((int)$user_id) . " AND `active`=1";
    $results = $db->query($query);
    if ($results) {
        $row = $results->fetch_assoc();
        if ($row && isset($row['num_following'])) {
            return (int)$row['num_following'];
        }
    }
    return 0;
}

function get_num_followers($user_id)
{
    global $db;
    $query = "SELECT COUNT(*) as `num_followers` FROM `follows`
              WHERE `user_destination_id`=" . $db->real_escape_string((int)$user_id) . " AND `active`=1";
    $results = $db->query($query);
    if ($results) {
        $row = $results->fetch_assoc();
        if ($row && isset($row['num_followers'])) {
            return (int)$row['num_followers'];
        }
    }
    return 0;
}

function get_num_newsfeed_posts($user_id)
{
    global $db;
    $query = "SELECT COUNT(*) AS `num_posts` FROM `posts`
          JOIN `users` ON `users`.`user_id`=`posts`.`user_id`
          WHERE `posts`.`active`=1
          AND 
          (
              `posts`.`user_id`=" . $db->real_escape_string($user_id) . "
              OR `posts`.`user_id` IN
              (
                  SELECT `user_destination_id` AS `user_id` FROM `follows`
                  WHERE `user_source_id`=" . $db->real_escape_string($user_id) . "
                  AND `active`=1
              )
          )";
    $results = $db->query($query);
    if ($results) {
        $row = $results->fetch_assoc();
        if ($row) {
            return (int)$row['num_posts'];
        }
    }
    return 0;
}

function get_num_favorites($post_id)
{
    global $db;
    $query = "SELECT COUNT(*) AS `num_favorites`
              FROM `favorites`
              WHERE `post_id`=" . $db->real_escape_string($post_id) . "
              AND `active`=1";
    $results = $db->query($query);
    if ($results && $results->num_rows) {
        $row = $results->fetch_assoc();
        if ($row) {
            return (int)$row['num_favorites'];
        }
    }
    return 0;
}

function get_posts($user_id, $result_start = 0, $num_results = 0)
{
    global $db;
    $query = "SELECT * FROM `posts`
              WHERE `user_id`=" . $db->real_escape_string((int)$user_id) . " AND `active`=1
              ORDER BY `date_created` DESC
              " . ($num_results ? "LIMIT $result_start, $num_results" : "");
    $results = $db->query($query);
    $post_array = array();
    if ($results) {
        while ($row = $results->fetch_assoc()) {
            $post_array[] = $row;
        }
    }
    return $post_array;
}

function get_following($user_id, $result_start = 0, $num_results = 0)
{
    global $db;
    $query = "SELECT `user_destination_id` AS `id` FROM `follows`
              WHERE `user_source_id`=" . $db->real_escape_string((int)$user_id) . " AND `active`=1
              ORDER BY `date_created` DESC
              " . ($num_results ? "LIMIT $result_start, $num_results" : "");
    $results = $db->query($query);
    $following_ids = array();
    if ($results) {
        while ($row = $results->fetch_assoc()) {
            $following_ids[] = (int)$row['id'];
        }
    }
    return $following_ids;
}

function get_followers($user_id, $result_start = 0, $num_results = 0)
{
    global $db;
    $query = "SELECT `user_source_id` AS `id` FROM `follows`
              WHERE `user_destination_id`=" . $db->real_escape_string((int)$user_id) . " AND `active`=1
              ORDER BY `date_created` DESC
              " . ($num_results ? "LIMIT $result_start, $num_results" : "");
    $results = $db->query($query);
    $follower_ids = array();
    if ($results) {
        while ($row = $results->fetch_assoc()) {
            $follower_ids[] = (int)$row['id'];
        }
    }
    return $follower_ids;
}

function get_newsfeed_posts($user_id, $result_start = 0, $num_results = 0)
{
    global $db;
    $query = "SELECT `posts`.`post_id`, `posts`.`content`, `posts`.`date_created`, 
              `users`.`user_id`, `users`.`name`, `users`.`handle`, `users`.`photo`
              FROM `posts`
              JOIN `users` ON `users`.`user_id`=`posts`.`user_id`
              WHERE `posts`.`active`=1
              AND
              (
                  `posts`.`user_id`=" . $db->real_escape_string($user_id) . "
                  OR `posts`.`user_id` IN
                  (
                      SELECT `user_destination_id` AS `user_id` FROM `follows`
                      WHERE `user_source_id`=" . $db->real_escape_string($user_id) . "
                      AND `active`=1
                  )
              )
              ORDER BY `posts`.`date_created` DESC
              " . ($num_results ? "LIMIT $result_start, $num_results" : "");;
    $results = $db->query($query);
    $posts = array();
    if ($results) {
        while ($row = $results->fetch_assoc()) {
            $posts[] = $row;
        }
    }
    return $posts;
}

function display_errors()
{
    global $ERRORS;

    echo "<div class=\"errors box\">\n";
    foreach ($ERRORS as $e) {
        echo "<div>$e</div>\n";
    }
    echo "</div>\n";
}

function display_notices()
{
    global $NOTICES;

    echo "<div class=\"notices box\">\n";
    foreach ($NOTICES as $n) {
        echo "<div>$n</div>\n";
    }
    echo "</div>\n";
}

function display_user($user_id)
{
    $user = get_user_by_id($user_id);
    if ($user) {
        $profile_link = SITE_ROOT . DIRECTORY_SEPARATOR . "view-profile.php?id=" . $user['user_id'];
        if (!$user['bio']) {
            $user['bio'] = "<em>empty bio</em>";
        } else if (strlen($user['bio']) > 100) {
            $user['bio'] = substr($user['bio'], 0, 100) . " ...";
        }
        echo "<div class=\"list-item\">\n";
        echo "\t<div class=\"list-item-photo\">\n";
        if ($user['photo']) {
            echo "\t\t<img src=\"".SITE_ROOT."/images/profile/".$user['user_id']."\" width=\"100%\" height=\"auto\" />\n";
        }
        echo "\t</div>\n";
        echo "\t<div class=\"list-item-header\">\n";
        echo "\t\t<div class=\"list-item-name\"><a href=\"$profile_link\">{$user['name']}</a></div>\n";
        echo "\t\t<div class=\"list-item-handle\"><a href=\"$profile_link\">@{$user['handle']}</a></div>\n";
        echo "\t</div>\n";
        echo "\t<div class=\"list-item-content\">{$user['bio']}</div>\n";
        echo "</div>\n";
    } else {
        return "";
    }
}

function display_post($user_id, $user_name, $user_handle, $user_photo, $post_content, $post_date, $post_id)
{
    $profile_link = SITE_ROOT . "/view-profile.php?id=" . $user_id;
    echo "<div class=\"list-item\" id=\"post-$post_id\">\n";
    echo "\t<div class=\"list-item-photo\">\n";
    if ($user_photo) {
        echo "\t\t<img src=\"".SITE_ROOT."/images/profile/".$user_id."\" width=\"100%\" height=\"auto\" />\n";
    }
    echo "\t</div>\n";
    echo "\t<div class=\"list-item-header\">\n";
    echo "\t\t<div class=\"list-item-name\"><a href=\"$profile_link\">$user_name</a></div>\n";
    echo "\t\t<div class=\"list-item-handle\"><a href=\"$profile_link\">@$user_handle</a></div>\n";
    echo "\t</div>\n";
    echo "\t<div class=\"list-item-content\">$post_content</div>\n";
    echo "\t<div class=\"list-item-footer\">\n";
    echo "\t\tPosted on <span class=\"list-item-footer-date\">$post_date</span>\n";
    if (is_logged_in()) {
        $favorited = is_favorited($post_id);
        $num_favorites = get_num_favorites($post_id);
        echo "\t\t| <span class=\"favorite\"><a onclick=\"javascript:favoritePost($post_id, this);\"
              href=\"javascript:void(0);\"" . ($favorited ? " class=\"selected\">Unfavorite" : ">Favorite") . " ($num_favorites)</a></span>\n";
        if ($_SESSION['user']['id'] == $user_id) {
            echo "\t\t| <a onclick=\"javascript:deletePost($post_id);\"
                  href=\"javascript:void(0);\">Delete</a>\n";
        }
    }
    echo "\t</div>\n";
    echo "</div>\n";
}

function display_posts_from_user($user_id, $result_start = 0, $num_results = 0)
{
    $user = get_user_by_id($user_id);
    $posts = get_posts($user_id, $result_start, $num_results);
    if ($user && count($posts) > 0) {
        foreach ($posts as $post) {
            display_post($user_id, $user['name'], $user['handle'], $user['photo'], $post['content'], $post['date_created'], $post['post_id']);
        }
    } else {
        echo "\t<p>There are no posts to display.</p>\n";
    }
}

function display_following($user_id, $result_start = 0, $num_results = 0)
{
    $following = get_following($user_id, $result_start, $num_results);
    if (count($following) > 0) {
        foreach ($following as $following_id) {
            display_user($following_id);
        }
    } else {
        echo "\t<p>There is nobody following this user.</p>\n";
    }
}

function display_followers($user_id, $result_start = 0, $num_results = 0)
{
    $followers = get_followers($user_id, $result_start, $num_results);
    if (count($followers) > 0) {
        foreach ($followers as $follower_id) {
            display_user($follower_id);
        }
    } else {
        echo "\t<p>This user has no followers.</p>\n";
    }
}

function display_follow_button($user_id)
{
    $follow_link = "javascript:followUser($user_id, this);";
    $button_text = is_following($user_id) ? "Unfollow" : "Follow";
    echo "<div class=\"align-right\">\n";
    echo "\t<span class=\"button\">\n";
    echo "\t\t<a onclick=\"" . $follow_link . "\">$button_text</a>\n";
    echo "\t</span>\n";
    echo "</div>\n";
}

function display_edit_profile_button()
{
    echo "<div class=\"align-right\">\n";
    echo "\t<span class=\"button\">\n";
    echo "\t\t<a href=\"" . SITE_ROOT . "/edit-profile.php\">Edit</a>\n";
    echo "\t</span>\n";
    echo "</div>\n";
}

function display_pagination($current_page, $last_page, $url)
{
    ?>
    <div class="pagination">
        <div class="prev-page">
            <?php if ($current_page > 1) { ?>
                <a href="<?php echo $url . "&p=" . ($current_page - 1); ?>">&laquo; Previous</a>
            <?php } ?>
        </div>
        <!--

             -->
        <div class="curr-page">
            <?php echo "Page $current_page"; ?>
        </div>
        <!--

             -->
        <div class="next-page">
            <?php if ($current_page < $last_page) { ?>
                <a href="<?php echo $url . "&p=" . ($current_page + 1); ?>">Next &raquo;</a>
            <?php } ?>
        </div>
    </div>
<?php
}

function display_create_post_form()
{
    ?>
    <form id="create-post-form" name="create-post-form" method="post">
        <div class="input-wrapper">
            <label for="create-post-content" class="input-required">Compose new post</label>
            <textarea class="text-input" id="create-post-content" name="create-post-content"
                      maxlength="<?php echo POST_MAX_LENGTH; ?>"
                ><?php if (isset($_POST['create-post-content'])) {
                    echo $_POST['create-post-content'];
                } ?></textarea>
            <script type="text/javascript">
                $(document).ready(function () {
                    $('#create-post-content').autosize();
                });
            </script>
        </div>
        <div class="submit-wrapper">
            <input class="submit-button" type="submit" name="create-post-submitted" value="Post"/>
        </div>
    </form>
<?php
}

function is_logged_in()
{
    return isset($_SESSION['user']['id']) && $_SESSION['user']['id'];
}

function is_following($user_id)
{
    global $db;

    if (!is_logged_in()) {
        return false;
    }

    $query = "SElECT `follow_id`
              FROM `follows` 
              WHERE `user_source_id`=" . $db->real_escape_string($_SESSION['user']['id']) . "
              AND `user_destination_id`=" . ((int)$user_id) . "
              AND `active`=1";
    $results = $db->query($query);
    return ($results && $results->num_rows);
}

function is_favorited($post_id)
{
    global $db;

    if (!is_logged_in()) {
        return false;
    }

    $query = "SELECT `favorite_id`
              FROM `favorites`
              WHERE `user_id`=" . $db->real_escape_string($_SESSION['user']['id']) . "
              AND `post_id`=" . ((int)$post_id) . "
              AND `active`=1";
    $results = $db->query($query);
    return ($results && $results->num_rows);
}

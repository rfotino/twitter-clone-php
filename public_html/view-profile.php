<?php

define("WEBPAGE_CONTEXT", "view-profile.php");
define("INCLUDE_HEADER", true);
define("RESULTS_PER_PAGE", 15);

set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__,
    __DIR__."/../resources"
)));

require_once("global.inc.php");
$JS_FILES[] = "js/follow.js";
$JS_FILES[] = "js/delete-post.js";

if (isset($_GET['id'])) {
    $user = get_user_by_id($_GET['id']);
    if (!$user) {
        $_SESSION['errors'][] = "Unable to find user.";
        header("Location: ".SITE_ROOT);
        exit;
    }
} else {
    header("Location: ".SITE_ROOT);
    exit;
}

if (isset($_GET['action'])) {
    $ACTION = $_GET['action'];
} else {
    $ACTION = "posts";
}

$MY_PROFILE = is_logged_in() && $user['user_id'] == $_SESSION['user']['id'];
$num_posts = get_num_posts($user['user_id']);
$num_following = get_num_following($user['user_id']);
$num_followers = get_num_followers($user['user_id']);

require_once("header.inc.php");

?>

<div class="box">

    <?php
    if ($user['photo']) {
        echo "<div class=\"profile-photo align-center\">\n";
        echo "<img src=\"".SITE_ROOT."/images/profile/".$user['user_id']."\" width=\"200px\" height=\"auto\" />\n";
        echo "</div>\n";
    }
    ?>
    <h1 class="align-center"><?php echo $user['name']; ?></h1>
    <h2 class="align-center">@<?php echo $user['handle']; ?></h2>
    <p class="align-center"><?php echo $user['bio']; ?></p>
    
    <?php
    if ($MY_PROFILE) {
        display_edit_profile_button();
    } else {
        display_follow_button($user['user_id']);
    }
    ?>
</div>

<div class="tabbed-box box">
    <a <?php echo $ACTION === "posts" ? "class=\"active\"" : ""; ?>
        href="<?php echo SITE_ROOT."/view-profile.php?id=".$user['user_id']; ?>&action=posts">
        Posts (<?php echo $num_posts; ?>)
    </a> |
    <a <?php echo $ACTION === "following" ? "class=\"active\"" : ""; ?>
        href="<?php echo SITE_ROOT."/view-profile.php?id=".$user['user_id']; ?>&action=following">
        Following (<?php echo $num_following; ?>)
    </a> |
    <a <?php echo $ACTION === "followers" ? "class=\"active\"" : ""; ?>
        href="<?php echo SITE_ROOT."/view-profile.php?id=".$user['user_id']; ?>&action=followers">
        Followers (<?php echo $num_followers; ?>)
    </a>
</div>

<div class="box">
    <?php
    
    switch ($ACTION) {
        case "posts": $num_items = $num_posts; break;
        case "following": $num_items = $num_following; break;
        case "followers": $num_items = $num_followers; break;
    }

    if (isset($_GET['p']) && (int)$_GET['p']) {
        $current_page = (int)$_GET['p'];
    } else {
        $current_page = 1;
    }

    $last_page = ceil($num_items / RESULTS_PER_PAGE);
    if ($current_page > $last_page) {
        $current_page = $last_page;
    } else if ($current_page < 1) {
        $current_page = 1;
    }

    switch ($ACTION) {
        case "posts":
            display_posts_from_user($user['user_id'], 
                ($current_page - 1) * RESULTS_PER_PAGE, 
                RESULTS_PER_PAGE);
            break;
        case "following":
            display_following($user['user_id'], 
                ($current_page - 1) * RESULTS_PER_PAGE, 
                RESULTS_PER_PAGE);
            break;
        case "followers":
            display_followers($user['user_id'], 
                ($current_page - 1) * RESULTS_PER_PAGE, 
                RESULTS_PER_PAGE);
            break;
    }

    if ($current_page) {
        display_pagination($current_page, $last_page, SITE_ROOT."/view-profile.php?id=".$user['user_id']."&action=".$ACTION);
    }
    ?>
</div>

<?php require_once("footer.inc.php");

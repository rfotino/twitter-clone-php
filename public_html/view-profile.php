<?php

define("WEBPAGE_CONTEXT", "view-profile.php");
define("INCLUDE_HEADER", true);

set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__,
    __DIR__."/../resources"
)));

require_once("global.inc.php");
$JS_FILES[] = "js/follow.js";

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

require_once("header.inc.php");

?>

<div class="box">
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
        href="<?php echo SITE_ROOT."/view-profile.php?id=".$_GET['id']; ?>&action=posts">
        Posts (<?php echo get_num_posts($user['user_id']); ?>)
    </a> |
    <a <?php echo $ACTION === "following" ? "class=\"active\"" : ""; ?>
        href="<?php echo SITE_ROOT."/view-profile.php?id=".$_GET['id']; ?>&action=following">
        Following (<?php echo get_num_following($user['user_id']); ?>)
    </a> |
    <a <?php echo $ACTION === "followers" ? "class=\"active\"" : ""; ?>
        href="<?php echo SITE_ROOT."/view-profile.php?id=".$_GET['id']; ?>&action=followers">
        Followers (<?php echo get_num_followers($user['user_id']); ?>)
    </a>
</div>

<?php 

switch ($ACTION) {
    case "posts":
        display_posts_from_user($user['user_id']);
        break;
    case "following":
        display_following($user['user_id']);
        break;
    case "followers":
        display_followers($user['user_id']);
        break;
}

require_once("footer.inc.php");

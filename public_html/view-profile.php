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

<?php require_once("footer.inc.php"); ?>

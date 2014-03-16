<?php

define("WEBPAGE_CONTEXT", "view-profile.php");
define("INCLUDE_HEADER", true);

set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__,
    __DIR__."/../resources"
)));

require_once("global.inc.php");

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

require_once("header.inc.php");

?>

<div class="box">
    <?php if (is_logged_in() && $_GET['id'] == $_SESSION['user']['id']) { ?>
    <div class="align-right">
        <span class="button"><a href="<?php echo SITE_ROOT.DIRECTORY_SEPARATOR."edit-profile.php"; ?>">Edit</a></span>
    </div>
    <?php } ?>
    <h1 class="align-center"><?php echo $user['name']; ?></h1>
    <h2 class="align-center">@<?php echo $user['handle']; ?></h2>
    <p class="align-center"><?php echo $user['bio']; ?></p>
</div>

<?php require_once("footer.inc.php"); ?>
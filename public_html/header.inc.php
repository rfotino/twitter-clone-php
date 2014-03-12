<?php
if (!defined("WEBPAGE_CONTEXT")) {
    header("Location: ".SITE_ROOT."/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?php echo isset($page_title) ? $page_title : $DEFAULT_PAGE_TITLE; ?></title>
	<?php
	foreach ($CSS_FILES as $css_file) {
	    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"css/$css_file\" />\n";
	}
	foreach ($JS_FILES as $js_file) {
	    echo "<script type=\"text/javascript\" src=\"$js_file\"></script>\n";
	}
	?>
    </head>
    <body<?php if (isset($ONLOAD)) { echo " onload=\"$ONLOAD\""; } ?>>
	<?php if (is_logged_in()) { ?>
	<div id="header">
	    <ul id="left-navbar" class="navbar header-item">
		<li class="navitem">
		    <a<?php if (WEBPAGE_CONTEXT == "index.php") { echo " class=\"current\""; } ?> href="<?php echo SITE_ROOT; ?>">Home</a>
		</li><!--
	     --><li class="navitem">
		    <a<?php if (WEBPAGE_CONTEXT == "notifications.php") { echo " class=\"current\""; } ?> href="<?php echo SITE_ROOT."/notifications.php"; ?>">Notifications</a>
		</li><!--
	     --><li class="navitem">
		    <a
		    <?php
		    if (WEBPAGE_CONTEXT == "view-profile.php" 
			    && isset($_GET['user_id']) 
			    && $_GET['user_id'] == $_SESSION['user']['id']) {
			echo "class=\"current\"";
		    }
		    ?>
		    href="<?php echo SITE_ROOT."/view-profile.php?id=".$_SESSION['user']['id']; ?>">My Profile</a>
		</li>
	    </ul><!--
	    
	 --><div id="search-wrapper" class="header-item">
		<form action="<?php echo SITE_ROOT."/search.php"; ?>" id="search-form" name="search-form" method="get">
		    <input type="text" id="search-input" name="q" /><input type="submit" id="search-submit" value="Search" />
		</form>
	    </div><!--
	    
	 --><ul id="right-navbar" class="navbar header-item">
		<li class="navitem">
		    <a<?php if (WEBPAGE_CONTEXT == "edit-profile.php") { echo " class=\"current\""; } ?> href="<?php echo SITE_ROOT."/edit-profile.php"; ?>">Edit Profile</a>
		</li><!--
	     --><li class="navitem">
		    <a href="<?php echo SITE_ROOT."/logout.php"; ?>">Logout</a>
		</li>
	    </ul>
	</div>
	<?php } ?>
	
	<?php
	if (count($NOTICES)) {
	    display_notices();
	}
	if (count($ERRORS)) {
	    display_errors();
	}
	?>
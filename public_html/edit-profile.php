<?php

define("WEBPAGE_CONTEXT", "login.php");
define("REQUIRES_LOGIN", true);

set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__,
    "../resources"
)));

require_once("global.inc.php");
$JS_FILES[] = "js/update-availability.js";

if (isset($_POST['edit-submitted'])) {
    $user_id = $_SESSION['user']['id'];
    $name = isset($_POST['edit-name']) ? trim($_POST['edit-name']) : "";
    $handle = isset($_POST['edit-handle']) ? trim($_POST['edit-handle']) : "";
    $bio = isset($_POST['edit-bio']) ? htmlentities(trim($_POST['edit-bio'])) : "";
    $email = isset($_POST['edit-email']) ? trim($_POST['edit-email']) : "";
    
    if ($name != $_SESSION['user']['name']) {
	if (!$name) {
	    $ERRORS[] = "Name must not be empty.";
	} else if (strlen($name) > 32) {
	    $ERRORS[] = "Name must not be more than 32 characters.";
	} else {
	    $query = "UPDATE `users` SET `name`='".$db->real_escape_string($name)."' WHERE `user_id`=".$db->real_escape_string($user_id);
	    $result = $db->query($query);
	    if ($result) {
		$_SESSION['user']['name'] = $name;
		$NOTICES[] = "Name has been updated successfully.";
	    } else {
		$ERRORS[] = "Name was unable to be updated.";
	    }
	}
    }
    
    if ($handle != $_SESSION['user']['handle']) {
	if (!$handle) {
	    $ERRORS[] = "Handle must not be empty.";
	} else if (strlen($handle) > 20) {
	    $ERRORS[] = "Handle must not be more than 20 characters.";
	} else if (get_user_by_handle($handle)) {
	    $ERRORS[] = "This handle is already in use.";
	} else {
	    $query = "UPDATE `users` SET `handle`='".$db->real_escape_string($handle)."' WHERE `user_id`=".$db->real_escape_string($user_id);
	    $result = $db->query($query);
	    if ($result) {
		$_SESSION['user']['handle'] = $handle;
		$NOTICES[] = "Handle has been updated successfully.";
	    } else {
		$ERRORS[] = "Handle was unable to be updated.";
	    }
	}
    }
    
    if ($bio != $_SESSION['user']['bio']) {
	if (strlen($bio) > 512) {
	    $ERRORS[] = "Bio must not be more than 512 characters.";
	} else {
	    $query = "UPDATE `users` SET `bio`='".$db->real_escape_string($bio)."' WHERE `user_id`=".$db->real_escape_string($user_id);
	    $result = $db->query($query);
	    if ($result) {
		$_SESSION['user']['bio'] = $bio;
		$NOTICES[] = "Bio has been updated successfully.";
	    } else {
		$ERRORS[] = "Bio was unable to be updated.";
	    }
	}
    }
    
    if ($email != $_SESSION['user']['email']) {
	if (!$email) {
	    $ERRORS[] = "Email must not be empty.";
	} else if (strlen($email) > 255) {
	    $ERRORS[] = "Email must not be more than 255 characters.";
	} else if (get_user_by_email($email)) {
	    $ERRORS[] = "This email is already in use.";
	} else {
	    $query = "UPDATE `users` SET `email`='".$db->real_escape_string($email)."' WHERE `user_id`=".$db->real_escape_string($user_id);
	    $result = $db->query($query);
	    if ($result) {
		$_SESSION['user']['email'] = $email;
		$NOTICES[] = "Email has been updated successfully.";
	    } else {
		$ERRORS[] = "Email was unable to be updated.";
	    }
	}
    }
} else if (isset($_POST['change-password-submitted'])) {
    $user_id = $_SESSION['user']['id'];
    
    $current_password = isset($_POST['current-password']) ? $_POST['current-password'] : "";
    $new_password = isset($_POST['new-password']) ? $_POST['new-password'] : "";
    $conf_password = isset($_POST['conf-password']) ? $_POST['conf-password'] : "";
    
    $current_user = get_this_user();
    $current_password_hash = $current_user['password'];
    
    if (!$current_password) {
	$ERRORS[] = "Current password must not be empty.";
    } else if (!password_verify($current_password, $current_password_hash)) {
	$ERRORS[] = "Your current password is incorrect.";
    } else if (!$new_password) {
	$ERRORS[] = "New password must not be empty.";
    } else if ($new_password != $conf_password) {
	$ERRORS[] = "New passwords do not match.";
    } else {
	$new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
	$query = "UPDATE `users` SET `password`='".$db->real_escape_string($new_password_hash)."' WHERE `user_id`=".$db->real_escape_string($user_id);
	$result = $db->query($query);
	if ($result) {
	    $NOTICES[] = "Password has been successfully changed.";
	} else {
	    $ERRORS[] = "Password unable to be changed.";
	}
    }
}

require_once("header.inc.php");

?>

<div class="form-wrapper box">
    <h2 class="form-title">Edit Profile</h2>
    <form id="edit-form" name="edit-form" method="post">
	<div class="input-wrapper">
	    <label for="edit-name" class="input-not-required">Name</label>
	    <input class="text-input" type="text" id="edit-name" name="edit-name"
		   value="<?php echo $_SESSION['user']['name']; ?>" />
	</div>
	<div class="input-wrapper">
	    <label for="edit-handle" class="input-not-required">Handle</label>
	    <input class="text-input" type="text" id="edit-handle" name="edit-handle"
		   onkeydown="javascript:updateHandleAvailability('edit-handle', 'edit-handle-available');"
		   onpaste="javascript:updateHandleAvailability('edit-handle', 'edit-handle-available');"
		   oninput="javascript:updateHandleAvailability('edit-handle', 'edit-handle-available');"
		   onblur="javascript:updateHandleAvailability('edit-handle', 'edit-handle-available');"
		   value="<?php echo $_SESSION['user']['handle']; ?>" />
	    <div class="handle-available" id="edit-handle-available"></div>
	</div>
	<div class="input-wrapper">
	    <label for="edit-bio" class="input-not-required">Bio</label>
	    <textarea class="text-input" id="edit-bio" name="edit-bio" maxlength="512"><?php echo $_SESSION['user']['bio']; ?></textarea>
	</div>
	<div class="input-wrapper">
	    <label for="edit-email" class="input-not-required">Email</label>
	    <input class="text-input email-input" type="email" id="edit-email" name="edit-email"
		   value="<?php echo $_SESSION['user']['email']; ?>" />
	</div>
	<div class="submit-wrapper">
	    <input class="submit-button" type="submit" name="edit-submitted" value="Edit" />
	</div>
    </form>
</div>

<div class="form-wrapper box">
    <h2 class="form-title">Change Password</h2>
    <form id="change-password-form" name="change-password-form" method="post">
	<div class="input-wrapper">
	    <label for="current-password" class="input-required">Current Password</label>
	    <input class="text-input password-input" type="password" id="current-password" name="current-password" />
	</div>
	<div class="input-wrapper">
	    <label for="new-password" class="input-required">New Password</label>
	    <input class="text-input password-input" type="password" id="new-password" name="new-password" />
	</div>
	<div class="input-wrapper">
	    <label for="conf-password" class="input-not-required">Confirm Password</label>
	    <input class="text-input password-input" type="password" id="conf-password" name="conf-password" />
	</div>
	<div class="submit-wrapper">
	    <input class="submit-button" type="submit" name="change-password-submitted" value="Change Password" />
	</div>
    </form>
</div>

<?php require_once("footer.inc.php"); ?>
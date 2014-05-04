<?php

define("WEBPAGE_CONTEXT", "edit-profile.php");
define("REQUIRES_LOGIN", true);

set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__,
    __DIR__ . "/../resources"
)));

require_once("global.inc.php");
$JS_FILES[] = "js/update-availability.js";
$JS_FILES[] = "js/jquery/jquery-2.1.0.min.js";
$JS_FILES[] = "js/jquery/plugins/autosize/jquery.autosize.min.js";

if (isset($_POST['edit-photo-submitted'])) {
    if (!isset($_FILES['profile-photo'])) {
        $ERRORS[] = "There was an error uploading your photo.";
    } else if ($_FILES['profile-photo']['error'] === UPLOAD_ERR_NO_FILE) {
        $ERRORS[] = "You must choose a photo to upload.";
    } else if ($_FILES['profile-photo']['error'] !== UPLOAD_ERR_OK) {
        $ERRORS[] = "There was an error uploading your photo.";
    } else if (!in_array($_FILES['profile-photo']['type'], $IMAGE_FORMATS)) {
        $ERRORS[] = "Invalid image format. Profile photo must be .jpg or .png.";
    }

    if (!$ERRORS) {
        if (move_uploaded_file($_FILES['profile-photo']['tmp_name'], __DIR__."/images/profile/".$_SESSION['user']['id'])) {
            $db->query("UPDATE `users` SET `photo`=1 WHERE `user_id`=".$db->real_escape_string((int)$_SESSION['user']['id']));
            $NOTICES[] = "Your photo has been uploaded successfully.";
            $_SESSION['user']['photo'] = 1;
        } else {
            $ERRORS[] = "There was an error uploading your photo.";
        }
    }
} else if (isset($_POST['edit-submitted'])) {
    $user_id = $_SESSION['user']['id'];
    $name = isset($_POST['edit-name']) ? trim($_POST['edit-name']) : "";
    $handle = isset($_POST['edit-handle']) ? trim($_POST['edit-handle']) : "";
    $bio = isset($_POST['edit-bio']) ? preg_replace('/\s+/', " ", htmlentities(trim($_POST['edit-bio']))) : "";
    $email = isset($_POST['edit-email']) ? trim($_POST['edit-email']) : "";

    if ($name != $_SESSION['user']['name']) {
        if (!$name) {
            $ERRORS[] = "Name must not be empty.";
        } else if (strlen($name) > 32) {
            $ERRORS[] = "Name must not be more than 32 characters.";
        } else {
            $query = "UPDATE `users` SET `name`='" . $db->real_escape_string($name) . "', `date_updated`=NOW() WHERE `user_id`=" . $db->real_escape_string($user_id);
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
            $query = "UPDATE `users` SET `handle`='" . $db->real_escape_string($handle) . "', `date_updated`=NOW() WHERE `user_id`=" . $db->real_escape_string($user_id);
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
            $query = "UPDATE `users` SET `bio`='" . $db->real_escape_string($bio) . "', `date_updated`=NOW() WHERE `user_id`=" . $db->real_escape_string($user_id);
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
            $query = "UPDATE `users` SET `email`='" . $db->real_escape_string($email) . "', `date_updated`=NOW() WHERE `user_id`=" . $db->real_escape_string($user_id);
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
        $query = "UPDATE `users` SET `password`='" . $db->real_escape_string($new_password_hash) . "', `date_updated`=NOW() WHERE `user_id`=" . $db->real_escape_string($user_id);
        $result = $db->query($query);
        if ($result) {
            $NOTICES[] = "Password has been successfully changed.";
        } else {
            $ERRORS[] = "Password unable to be changed.";
        }
    }
} else if (isset($_POST['delete-account-submitted'])) {
    $query = "DELETE FROM `users` WHERE `user_id`=" . $db->real_escape_string($_SESSION['user']['id']);
    $result = $db->query($query);
    if ($result) {
        logout();
        $_SESSION['notices'][] = "Your account has been deleted.";
        header("Location: " . SITE_ROOT . DIRECTORY_SEPARATOR . "login.php");
        exit;
    } else {
        $ERRORS[] = "Failed to delete account.";
    }
}

require_once("header.inc.php");

?>

    <div class="form-wrapper box">
        <h2 class="form-title">Edit Photo</h2>

        <?php
        if ($_SESSION['user']['photo']) {
            echo "<div class=\"profile-photo align-center\">\n";
            echo "<img src=\"".SITE_ROOT."/images/profile/".$_SESSION['user']['id']."\" width=\"200px\" height=\"auto\" />\n";
            echo "</div>\n";
        }
        ?>

        <form id="edit-photo-form" name="edit-photo-form" method="post" enctype="multipart/form-data">
            <div class="input-wrapper">
                <label for="profile-photo" class="input-required">Profile Photo</label>
                <input class="file-input" type="file" name="profile-photo" id="profile-photo" />
            </div>

            <div class="submit-wrapper">
                <input class="submit-button" type="submit" name="edit-photo-submitted" value="Upload"/>
            </div>
        </form>
    </div>

    <div class="form-wrapper box">
        <h2 class="form-title">Edit Profile</h2>

        <form id="edit-form" name="edit-form" method="post">
            <div class="input-wrapper">
                <label for="edit-name" class="input-not-required">Name</label>
                <input class="text-input" type="text" id="edit-name" name="edit-name"
                       value="<?php echo $_SESSION['user']['name']; ?>"/>
            </div>
            <div class="input-wrapper">
                <label for="edit-handle" class="input-not-required">Handle</label>
                <input class="text-input" type="text" id="edit-handle" name="edit-handle"
                       onkeydown="javascript:updateHandleAvailability('edit-handle', 'edit-handle-available');"
                       onpaste="javascript:updateHandleAvailability('edit-handle', 'edit-handle-available');"
                       oninput="javascript:updateHandleAvailability('edit-handle', 'edit-handle-available');"
                       onblur="javascript:updateHandleAvailability('edit-handle', 'edit-handle-available');"
                       value="<?php echo $_SESSION['user']['handle']; ?>"/>

                <div class="handle-available" id="edit-handle-available"></div>
            </div>
            <div class="input-wrapper">
                <label for="edit-bio" class="input-not-required">Bio</label>
                <textarea class="text-input" id="edit-bio" name="edit-bio"
                          maxlength="512"><?php echo $_SESSION['user']['bio']; ?></textarea>
                <script type="text/javascript">
                    $(document).ready(function () {
                        $('#edit-bio').autosize();
                    });
                </script>
            </div>
            <div class="input-wrapper">
                <label for="edit-email" class="input-not-required">Email</label>
                <input class="text-input email-input" type="email" id="edit-email" name="edit-email"
                       value="<?php echo $_SESSION['user']['email']; ?>"/>
            </div>
            <div class="submit-wrapper">
                <input class="submit-button" type="submit" name="edit-submitted" value="Edit"/>
            </div>
        </form>
    </div>

    <div class="form-wrapper box">
        <h2 class="form-title">Change Password</h2>

        <form id="change-password-form" name="change-password-form" method="post">
            <div class="input-wrapper">
                <label for="current-password" class="input-required">Current Password</label>
                <input class="text-input password-input" type="password" id="current-password" name="current-password"/>
            </div>
            <div class="input-wrapper">
                <label for="new-password" class="input-required">New Password</label>
                <input class="text-input password-input" type="password" id="new-password" name="new-password"/>
            </div>
            <div class="input-wrapper">
                <label for="conf-password" class="input-not-required">Confirm Password</label>
                <input class="text-input password-input" type="password" id="conf-password" name="conf-password"/>
            </div>
            <div class="submit-wrapper">
                <input class="submit-button" type="submit" name="change-password-submitted" value="Change Password"/>
            </div>
        </form>
    </div>

    <div class="form-wrapper box">
        <h2 class="form-title">Delete Account</h2>

        <p>Clicking this button will permanently delete your account. Make sure you want to do this before you click
            'Delete Account'.</p>

        <form id="delete-account-form" name="delete-account-form" method="post"
              onsubmit="javascript:return confirm('Are you sure you want to permanently delete your account? This action cannot be undone.');">
            <div class="submit-wrapper">
                <input class="submit-button" type="submit" name="delete-account-submitted" value="Delete Account"/>
            </div>
        </form>
    </div>

<?php require_once("footer.inc.php"); ?>
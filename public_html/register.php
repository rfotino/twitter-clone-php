<?php

define("WEBPAGE_CONTEXT", "register.php");

set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__,
    __DIR__ . "/../resources"
)));

require_once("global.inc.php");
$JS_FILES[] = "js/update-availability.js";
$JS_FILES[] = "js/jquery/jquery-2.1.0.min.js";
$JS_FILES[] = "js/jquery/plugins/autosize/jquery.autosize.min.js";

if (isset($_POST['register-submitted'])) {
    $register_name = isset($_POST['register-name']) ? trim($_POST['register-name']) : "";
    $register_handle = isset($_POST['register-handle']) ? trim($_POST['register-handle']) : "";
    $register_bio = isset($_POST['register-bio']) ? preg_replace('/\s+/', " ", htmlentities(trim($_POST['register-bio']))) : "";
    $register_email = isset($_POST['register-email']) ? trim($_POST['register-email']) : "";
    $register_password = isset($_POST['register-password']) ? $_POST['register-password'] : "";
    $register_conf_password = isset($_POST['register-conf-password']) ? $_POST['register-conf-password'] : "";

    if (isset($_FILES['register-photo']) && $_FILES['register-photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['register-photo']['error']) {
            $ERRORS[] = "There was a problem uploading your photo.";
        } else if (!in_array($_FILES['register-photo']['type'], $IMAGE_FORMATS)) {
            $ERRORS[] = "Invalid image format. Profile photo must be .jpg or .png.";
        }
    }

    if (!$register_name) {
        $ERRORS[] = "Name is a required field.";
    } else if (strlen($register_name) > 32) {
        $ERRORS[] = "Name cannot be more than 32 characters.";
    }

    if (!$register_handle) {
        $ERRORS[] = "Handle is a required field.";
    } else if (strlen($register_handle) > 20) {
        $ERRORS[] = "Handle cannot be more than 20 characters.";
    } else if (get_user_by_handle($register_handle)) {
        $ERRORS[] = "The handle \"$register_handle\" is already registered.";
    }

    if (strlen($register_bio) > BIO_MAX_LENGTH) {
        $ERRORS[] = "Bio cannot be more than " . BIO_MAX_LENGTH . " characters.";
    }

    if (!$register_email) {
        $ERRORS[] = "Email is a required field.";
    } else if (strlen($register_email) > 255) {
        $ERRORS[] = "Email cannot be more than 255 characters.";
    } else if (get_user_by_email($register_email)) {
        $ERRORS[] = "The email \"$register_email\" is already registered.";
    }

    if (!$register_password) {
        $ERRORS[] = "Password is a required field.";
    } else if ($register_password != $register_conf_password) {
        $ERRORS[] = "Passwords do not match.";
    }

    if (!count($ERRORS)) {
        $password_hash = password_hash($register_password, PASSWORD_DEFAULT);

        $query = "INSERT INTO `users`
		  (`name`, `handle`, `bio`, `email`, `password`)
		  VALUES ('" . $db->real_escape_string($register_name) . "',
		      '" . $db->real_escape_string($register_handle) . "',
		      '" . $db->real_escape_string($register_bio) . "',
		      '" . $db->real_escape_string($register_email) . "',
		      '" . $db->real_escape_string($password_hash) . "')";
        $result = $db->query($query);
        if ($result) {
            if ($_FILES['register-photo']['error'] === UPLOAD_ERR_OK) {
                $user_id = $db->insert_id;
                if (move_uploaded_file($_FILES['register-photo']['tmp_name'], __DIR__."/images/profile/".$db->insert_id)) {
                    $db->query("UPDATE `users` SET `photo`=1 WHERE `user_id`=".$db->real_escape_string((int)$user_id));
                } else {
                    $ERRORS[] = "Failed to upload profile photo.";
                }
            }

            header("Location: " . SITE_ROOT . "/send-activation.php?user_id=" . urlencode($db->insert_id));
            exit;
        } else {
            $ERRORS[] = "Failed to create new user.";
        }
    }
}

require_once("header.inc.php");

?>

    <div class="form-wrapper box">
        <h2 class="form-title">Register</h2>

        <form id="register-form" name="register-form" method="post" enctype="multipart/form-data">
            <div class="input-wrapper">
                <label for="register-photo" class="input-not-required">Profile Photo</label>
                <input class="file-input" type="file" name="register-photo" id="register-photo"/>
            </div>

            <div class="input-wrapper">
                <label for="register-name" class="input-required">Name</label>
                <input class="text-input" type="text" id="register-name" name="register-name"
                       value="<?php echo isset($_POST['register-name']) ? $_POST['register-name'] : ""; ?>"/>
            </div>

            <div class="input-wrapper">
                <label for="register-handle" class="input-required">Handle</label>
                <input class="text-input" type="text" id="register-handle" name="register-handle"
                       onkeydown="javascript:updateHandleAvailability('register-handle', 'register-handle-available');"
                       onpaste="javascript:updateHandleAvailability('register-handle', 'register-handle-available');"
                       oninput="javascript:updateHandleAvailability('register-handle', 'register-handle-available');"
                       onblur="javascript:updateHandleAvailability('register-handle', 'register-handle-available');"
                       value="<?php echo isset($_POST['register-handle']) ? $_POST['register-handle'] : ""; ?>"/>

                <div class="handle-available" id="register-handle-available"></div>
            </div>

            <div class="input-wrapper">
                <label for="register-bio" class="input-not-required">Bio</label>
                <textarea class="text-input textarea-input" id="register-bio" name="register-bio"
                          maxlength="512"><?php echo isset($_POST['register-bio']) ? $_POST['register-bio'] : ""; ?></textarea>
                <script type="text/javascript">
                    $(document).ready(function () {
                        $('#register-bio').autosize();
                    });
                </script>
            </div>

            <div class="input-wrapper">
                <label for="register-email" class="input-required">Email</label>
                <input class="text-input email-input" type="email" id="register-email" name="register-email"
                       value="<?php echo isset($_POST['register-email']) ? $_POST['register-email'] : ""; ?>"/>
            </div>

            <div class="input-wrapper">
                <label for="register-password" class="input-required">Password</label>
                <input class="text-input password-input" type="password" id="register-password"
                       name="register-password"/>
            </div>

            <div class="input-wrapper">
                <label for="register-conf-password" class="input-required">Confirm Password</label>
                <input class="text-input password-input" type="password" id="register-conf-password"
                       name="register-conf-password"/>
            </div>

            <div class="submit-wrapper">
                <input class="submit-button" type="submit" name="register-submitted" value="Register"/>
                or
                <a href="<?php echo SITE_ROOT . "/login.php"; ?>">Cancel</a>
            </div>
        </form>
    </div>

<?php
require_once("footer.inc.php");

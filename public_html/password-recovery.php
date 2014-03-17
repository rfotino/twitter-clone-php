<?php

define("WEBPAGE_CONTEXT", "password-recovery.php");

set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__,
    __DIR__."/../resources"
)));

require_once("global.inc.php");

$show_change_password_form = false;
if (isset($_GET['user_id']) && isset($_GET['hash'])) {
    $user_id = $_GET['user_id'];
    $recover_hash = $_GET['hash'];
    
    $query = "SELECT * FROM `password_recovery`
	      WHERE `user_id`=".$db->real_escape_string($user_id)." 
	      AND `recover_hash`='".$db->real_escape_string($recover_hash)."'
	      ORDER BY `date_created` DESC";
    $result = $db->query($query);
    if ($result) {
	$recovery_info = $result->fetch_assoc();
	if ($recovery_info['date_used']) {
	    $ERRORS[] = "This recovery link has already been used!";
	} else {
	    $today = date("Y-m-d H:i:s");
	    if ($today < $recovery_info['date_inactive']) {
		$show_change_password_form = true;
		if (isset($_POST['change-password-submitted'])) {
		    $new_password = isset($_POST['new-password']) ? $_POST['new-password'] : "";
		    $conf_password = isset($_POST['conf-password']) ? $_POST['conf-password'] : "";

		    if (!$new_password) {
			$ERRORS[] = "You must choose a password!";
		    } else if ($new_password != $conf_password) {
			$ERRORS[] = "Passwords must match!";
		    } else {
			$password_hash = password_hash($new_password, PASSWORD_DEFAULT);
			$query = "UPDATE `password_recovery`
				  SET `date_used`='".date("Y-m-d H:i:s")."'
				  WHERE `user_id`=".$db->real_escape_string($user_id)."
				  AND `recover_hash`='".$db->real_escape_string($recover_hash)."'";
			$result = $db->query($query);
			if (!$result) {
			    $ERRORS[] = "Unable to set recovery hash as used!";
			} else {
			    $query = "UPDATE `users`
				      SET `password`='".$db->real_escape_string($password_hash)."'
				      WHERE `user_id`=".$db->real_escape_string($user_id);
			    $result = $db->query($query);
			    if ($result) {
				$_SESSION['notices'][] = "Your password has been successfully updated.";
				header("Location: ".SITE_ROOT.DIRECTORY_SEPARATOR."login.php");
				exit;
			    } else {
				$ERRORS[] = "Unable to update password hash!";
			    }
			}
		    }
		}
	    } else {
		$ERRORS[] = "Recovery token has expired.";
	    }
	}
    } else {
	$ERRORS[] = "Invalid recovery token.";
    }
}

if (isset($_POST['recovery-submitted'])) {
    $email = isset($_POST['recovery-email']) ? $_POST['recovery-email'] : "";
    
    if (!$email) { $ERRORS[] = "You must submit an email."; }
    
    if (!count($ERRORS)) {
	$user = get_user_by_email($email);
	if (!$user) {
	    $ERRORS[] = "No user exists with this email.";
	}
	
	if (!count($ERRORS)) {
	    $recover_hash = sha1(uniqid());
	    
	    $query = "INSERT INTO `password_recovery` 
		      (`user_id`, `recover_hash`, `date_inactive`) 
		      VALUES (".
		          $db->real_escape_string($user['user_id']).", 
			  '".$db->real_escape_string($recover_hash)."',
			  '".date("Y-m-d H:i:s", time() + 3600)."'
		      )";
	    $result = $db->query($query);
	    if (!$result) {
		$ERRORS[] = "Unable to insert recovery hash.";
	    }
	    
	    if (!count($ERRORS)) {
		$subject = "Twitter Clone Password Recovery";
		
		$activation_link = SITE_ROOT.DIRECTORY_SEPARATOR."password-recovery.php?user_id=".$user['user_id']."&hash=".$recover_hash;
		$message = "<html>";
		$message .= "<p>Dearest {$user['handle']},</p>";
		$message .= "<p>You wished to recovery your password for your account on the Twitter Clone. ";
		$message .= "If you still wish to do so, please follow this <a href=\"".$activation_link."\">link</a>. ";
		$message .= "Otherwise, feel free to disregard this message. The link will expire in time.</p>";
		$message .= "<p>Thank you,<br />The Twitter Clone Team.</p>";
		$message .= "</html>";
		
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$headers .= "From: Twitter Clone <noreply@".$_SERVER['HTTP_HOST'].">\r\n";
                $headers .= "Reply-To: Twitter Clone <noreply@".$_SERVER['HTTP_HOST'].">\r\n";
		$headers .= "X-Mailer: PHP/".phpversion()."\r\n";

		if (defined("EMAIL_ENABLED") && EMAIL_ENABLED) {
                    $_SESSION['notices'][] = "Your recovery email has been sent.";
		    mail($email, $subject, $message, $headers);
		    header("Location: ".SITE_ROOT.DIRECTORY_SEPARATOR."login.php");
		} else {
		    echo $message;
		}
		exit;
	    }
	}
    }
}

require_once("header.inc.php");

if ($show_change_password_form) {
?>

<div class="form-wrapper box">
    <h2 class="form-title">Change Password</h2>
    <form id="change-password-form" name="change-password-form" method="post">
	<div class="input-wrapper">
	    <label for="new-password" class="input-required">New Password</label>
	    <input class="text-input password-input" type="password" id="new-password" name="new-password" />
	</div>
	<div class="input-wrapper">
	    <label for="conf-password" class="input-required">Confirm Password</label>
	    <input class="text-input password-input" type="password" id="conf-password" name="conf-password" />
	</div>
	<div class="submit-wrapper">
	    <input class="submit-button" type="submit" name="change-password-submitted" />
	    or 
	    <a href="<?php echo SITE_ROOT."/login.php"; ?>">Cancel</a>
	</div>
    </form>
</div>

<?php } else { ?>

<div class="form-wrapper box">
    <h2 class="form-title">Recover Password</h2>
    <form id="recovery-form" name="recovery-form" method="post" action="?">
	<div class="input-wrapper">
	    <label for="recovery-email">Email</label>
	    <input class="text-input email-input" type="email" id="recovery-email" name="recovery-email"
		   value="<?php echo isset($_POST['recovery-email']) ? $_POST['recovery-email'] : ""; ?>" />
	</div>
	<div class="submit-wrapper">
	    <input class="submit-button" type="submit" name="recovery-submitted" value="Send Recovery Email" />
	    or 
	    <a href="<?php echo SITE_ROOT."/login.php"; ?>">Cancel</a>
	</div>
    </form>
</div>

<?php } ?>

<?php require_once("footer.inc.php"); ?>

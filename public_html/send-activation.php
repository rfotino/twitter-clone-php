<?php

define("WEBPAGE_CONTEXT", "send-activation.php");

set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__,
    "../resources"
)));

require_once("global.inc.php");

if (isset($_GET['user_id']) && ((int)$_GET['user_id'])) {
    $user_id = (int)$_GET['user_id'];
    $user = get_user_by_id($user_id);
    $activation_hash = sha1(rand());
    
    $query = "UPDATE `users` 
	      SET `activate_hash`='".$db->real_escape_string($activation_hash)."' 
	      WHERE `user_id`=".$db->real_escape_string($user_id);
    $result = $db->query($query);
    
    if ($result && $user) {
	$subject = "twitter-clone-php Account Activation";

	$activation_link = SITE_ROOT."/activate.php?hash=$activation_hash";
	$message = "<html>\n";
	$message .= "Thank you for registering, {$user['name']}.\n\n";
	$message .= "Your account is not yet activated. Click <a href=\"$activation_link\">here</a> to activate.\n\n";
	$message .= "Thank you,\nThe twitter-clone-php Team\n";
	$message .= "</html>";

	$headers = "From: twitter-clone-php <register@twitter.com>\n";

	if (defined("EMAIL_ENABLED") && EMAIL_ENABLED) {
	    mail($user['email'], $subject, $message, $headers);
	} else {
	    echo $message;
	    exit;
	}
    }
}

header("Location: ".SITE_ROOT."/login.php");
exit;

?>

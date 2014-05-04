<?php

define("WEBPAGE_CONTEXT", "send-activation.php");

set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__,
    __DIR__ . "/../resources"
)));

require_once("global.inc.php");

if (isset($_GET['user_id']) && ((int)$_GET['user_id'])) {
    $user_id = (int)$_GET['user_id'];
    $user = get_user_by_id($user_id);
    $activation_hash = sha1(rand());

    $query = "UPDATE `users` 
	      SET `activate_hash`='" . $db->real_escape_string($activation_hash) . "'
	      WHERE `user_id`=" . $db->real_escape_string($user_id);
    $result = $db->query($query);

    if ($result && $user) {
        $subject = "Twitter Clone Account Activation";

        $activation_link = SITE_ROOT . "/activate.php?hash=$activation_hash";
        $message = "<html>\n";
        $message .= "<p>Thank you for registering, {$user['name']}.</p>\n\n";
        $message .= "<p>Your account is not yet activated. Click <a href=\"$activation_link\">here</a> to activate.</p>\n\n";
        $message .= "<p>Thank you,\nThe Twitter Clone Team</p>\n";
        $message .= "</html>";

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $headers .= "From: Twitter Clone <noreply@" . $_SERVER['HTTP_HOST'] . ">\r\n";
        $headers .= "Reply-To: Twitter Clone <noreply@" . $_SERVER['HTTP_HOST'] . ">\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

        if (defined("EMAIL_ENABLED") && EMAIL_ENABLED) {
            $_SESSION['notices'][] = "Your activation email has been sent.";
            mail($user['email'], $subject, $message, $headers);
        } else {
            echo $message;
            exit;
        }
    }
}

header("Location: " . SITE_ROOT . "/login.php");
exit;

?>

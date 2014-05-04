<?php

define("WEBPAGE_CONTEXT", "login.php");

set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__,
    __DIR__."/../resources"
)));

require_once("global.inc.php");

if (isset($_POST['login-submitted'])) {
    $user = isset($_POST['login-user']) ? trim($_POST['login-user']) : "";
    $password = isset($_POST['login-password']) ? $_POST['login-password'] : "";
    
    if (!$user) {
	$ERRORS[] = "Email or handle is a required field.";
    }
    if (!$password) {
	$ERRORS[] = "Password is a required field.";
    }
    
    if (!count($ERRORS)) {
	if (!($user_info = get_user_by_email($user)) && !($user_info = get_user_by_handle($user))) {
	    $ERRORS[] = "Invalid login.";
	} else if (!password_verify($password, $user_info['password'])) {
	    $ERRORS[] = "Invalid login.";
	} else if (!$user_info['active']) {
	    $send_activation_link = SITE_ROOT."/send-activation.php?user_id=".$user_info['user_id'];
	    $ERRORS[] = "User is not yet activated. (<a href=\"$send_activation_link\">Resend Activation Email</a>)";
	} else {
	    $_SESSION['user']['id'] = $user_info['user_id'];
	    $_SESSION['user']['name'] = $user_info['name'];
	    $_SESSION['user']['handle'] = $user_info['handle'];
	    $_SESSION['user']['bio'] = $user_info['bio'];
	    $_SESSION['user']['email'] = $user_info['email'];
        $_SESSION['user']['photo'] = $user_info['photo'];
	    
	    header("Location: ".(isset($_GET['redirect']) ? $_GET['redirect'] : SITE_ROOT.DIRECTORY_SEPARATOR."index.php"));
	    exit;
	}
    }
}

require_once("header.inc.php");

?>

<div class="form-wrapper box">
    <h2 class="form-title">Login</h2>
    <form id="login-form" name="login-form" method="post">
	<div class="input-wrapper">
	    <label for="login-user" class="input-required">Email or handle</label>
	    <input class="text-input" type="text" id="login-user" name="login-user"
		   value="<?php echo isset($_POST['login-user']) ? $_POST['login-user'] : ""; ?>" />
	</div>
	<div class="input-wrapper">
	    <label for="login-password" class="input-required">Password</label>
	    <input class="text-input password-input" type="password" id="login-password" name="login-password" />
	</div>
	<div class="submit-wrapper">
	    <input class="submit-button" type="submit" name="login-submitted" value="Login" />
	</div>
    </form>
</div>

<div class="box">
    <div><a href="register.php">Register</a></div>
    <div><a href="password-recovery.php">Recover Password</a></div>
</div>

<?php require_once("footer.inc.php"); ?>
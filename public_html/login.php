<?php

define("WEBPAGE_CONTEXT", "login.php");

set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__,
    "../resources"
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
	} else {
	    $_SESSION['user']['id'] = $user_info['user_id'];
	    $_SESSION['user']['name'] = $user_info['name'];
	    $_SESSION['user']['handle'] = $user_info['handle'];
	    $_SESSION['user']['email'] = $user_info['email'];
	    
	    header("Location: ".SITE_ROOT."/".(isset($_GET['redirect']) ? $_GET['redirect'] : "index.php"));
	    exit;
	}
    }
}

require_once("header.inc.php");

?>

<div class="form-wrapper">
    <h2 class="form-title">Login</h2>
    <form id="login-form" name="login-form" method="post">
	<div class="input-wrapper">
	    <label for="login-user">Email or handle</label>
	    <input class="text-input" type="text" id="login-user" name="login-user"
		   value="<?php echo isset($_POST['login-user']) ? $_POST['login-user'] : ""; ?>" />
	</div>
	<div class="input-wrapper">
	    <label for="login-password">Password</label>
	    <input class="text-input password-input" type="password" id="login-password" name="login-password" />
	</div>
	<div class="submit-wrapper">
	    <input class="submit-buttom" type="submit" name="login-submitted" value="Login" />
	</div>
    </form>
</div>
<p><a href="register.php">Register</a></p>

<?php require_once("footer.inc.php"); ?>
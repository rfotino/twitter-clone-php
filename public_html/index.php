<?php

define("WEBPAGE_CONTEXT", "index.php");
define("REQUIRES_LOGIN", true);

set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__,
    __DIR__."/../resources"
)));

require_once("global.inc.php");
$JS_FILES[] = "js/jquery/jquery-2.1.0.min.js";
$JS_FILES[] = "js/jquery/plugins/autosize/jquery.autosize.min.js";

if (isset($_POST['create-post-submitted'])) {
    $post_content = isset($_POST['create-post-content']) ? preg_replace('/\s+/', " ", htmlentities(trim($_POST['create-post-content']))) : "";
    if (!$post_content) {
        $ERRORS[] = "Your post must not be empty.";
    } else if (strlen($post_content) > POST_MAX_LENGTH) {
        $ERRORS[] = "Your post must not be more than ".POST_MAX_LENGTH." characters.";
    }
    
    if (!$ERRORS) {
        $query = "INSERT INTO `posts`
                  (`user_id`, `content`)
                  VALUES
                  (".$db->real_escape_string($_SESSION['user']['id']).",
                  '".$db->real_escape_string($post_content)."')";
        $result = $db->query($query);
        if ($result) {
            $_SESSION['notices'][] = "Post successful.";
            header("Location: ".SITE_ROOT);
            exit;
        } else {
            $ERRORS[] = "We apologize, but we were unable to submit your post. Please try again later.";
        }
    }
}

require_once("header.inc.php");

display_create_post_form();

require_once("footer.inc.php");

?>
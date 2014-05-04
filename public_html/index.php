<?php

define("WEBPAGE_CONTEXT", "index.php");
define("REQUIRES_LOGIN", true);
define("RESULTS_PER_PAGE", 15);

set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__,
    __DIR__."/../resources"
)));

require_once("global.inc.php");
$JS_FILES[] = "js/jquery/plugins/autosize/jquery.autosize.min.js";
$JS_FILES[] = "js/delete-post.js";

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

echo "<div class=\"form-wrapper box\">\n";
display_create_post_form();
echo "</div>\n";

echo "<div class=\"box\">\n";
$num_posts = get_num_newsfeed_posts($_SESSION['user']['id']);

if ($num_posts) {
    if (isset($_GET['p']) && (int)$_GET['p']) {
        $current_page = (int)$_GET['p'];
    } else {
        $current_page = 1;
    }

    $last_page = ceil($num_posts / RESULTS_PER_PAGE);
    if ($current_page > $last_page) {
        $current_page = $last_page;
    } else if ($current_page < 1) {
        $current_page = 1;
    }
    
    $posts = get_newsfeed_posts($_SESSION['user']['id'], ($current_page - 1) * RESULTS_PER_PAGE, RESULTS_PER_PAGE);
    foreach ($posts as $p) {
        display_post($p['user_id'], $p['name'], $p['handle'], $p['photo'], $p['content'], $p['date_created'], $p['post_id']);
    }
    
    display_pagination($current_page, $last_page, SITE_ROOT."/?");
} else {
    echo "\t<p>There are no posts to display!</p>\n";
}

echo "</div>\n";

require_once("footer.inc.php");

?>
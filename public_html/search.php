<?php

define("WEBPAGE_CONTEXT", "search.php");
define("INCLUDE_HEADER", true);
define("RESULTS_PER_PAGE", 15);

set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__,
    __DIR__."/../resources"
)));

require_once("global.inc.php");

$user_query = preg_replace('/\s+/', " ", trim(htmlentities(isset($_GET['q']) ? $_GET['q'] : "")));

require_once("header.inc.php");

?>

<div class="box">
    <?php
    
    $count_query = "SELECT COUNT(*) AS `num_results` FROM `users`
                    WHERE `handle` LIKE '%".$db->real_escape_string($user_query)."%'
                    OR `name` LIKE '%".$db->real_escape_string($user_query)."%'
                    OR `bio` LIKE '%".$db->real_escape_string($user_query)."%'";
    $count_results = $db->query($count_query);
    if ($count_results && $count_results->num_rows) {
        $count_result_row = $count_results->fetch_assoc();
        $num_results = (int)$count_result_row['num_results'];
    } else {
        $num_results = 0;
    }
    
    if (isset($_GET['p']) && (int)$_GET['p']) {
        $current_page = (int)$_GET['p'];
    } else {
        $current_page = 1;
    }
    
    $last_page = ceil($num_results / RESULTS_PER_PAGE);
    if ($current_page > $last_page) {
        $current_page = $last_page;
    } else if ($current_page < 1) {
        $current_page = 1;
    }
    
    ?>
    
    <h2>Results for "<?php echo $user_query; ?>"</h2>
    
    <?php
    
    $query = "SELECT * FROM `users`
              WHERE `handle` LIKE '%".$db->real_escape_string($user_query)."%'
              OR `name` LIKE '%".$db->real_escape_string($user_query)."%'
              OR `bio` LIKE '%".$db->real_escape_string($user_query)."%'
              LIMIT ".(($current_page - 1) * RESULTS_PER_PAGE).", ".RESULTS_PER_PAGE;
    $results = $db->query($query);
    if ($results && $results->num_rows) {
        while ($row = $results->fetch_assoc()) {
            echo display_user($row['user_id']);
        }
        display_pagination($current_page, $last_page, SITE_ROOT."/search.php?q=".urlencode($_GET['q']));
    } else {
        echo "<p>No matching users found!</p>";
    }
    
    ?>
</div>

<?php require_once("footer.inc.php");
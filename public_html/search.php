<?php

define("WEBPAGE_CONTEXT", "edit-profile.php");
define("INCLUDE_HEADER", true);

set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__,
    "../resources"
)));

require_once("global.inc.php");

$user_query = preg_replace('/\s+/', " ", trim(htmlentities(isset($_GET['q']) ? $_GET['q'] : "")));

require_once("header.inc.php");

?>

<div class="box">
    <h2>Results for "<?php echo $user_query; ?>"</h2>
    <?php
    $query = "SELECT * FROM `users`
              WHERE `handle` LIKE '%".$db->real_escape_string($user_query)."%'
              OR `name` LIKE '%".$db->real_escape_string($user_query)."%'
              OR `bio` LIKE '%".$db->real_escape_string($user_query)."%'
              LIMIT 15";
    $results = $db->query($query);
    if ($results && $results->num_rows) {
        while ($row = $results->fetch_assoc()) {
            echo display_user($row['user_id']);
        }
    } else {
        echo "<p>No matching users found!</p>";
    }
    ?>
</div>

<?php require_once("footer.inc.php"); ?>
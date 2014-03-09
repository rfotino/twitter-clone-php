<?php

define("WEBPAGE_CONTEXT", "index.php");
define("REQUIRES_LOGIN", true);

set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__,
    "../resources"
)));

require_once("global.inc.php");
require_once("header.inc.php");

?>

<p><a href="edit-profile.php">Edit Profile</a></p>
<p><a href="logout.php">Logout</a></p>

<?php require_once("footer.inc.php"); ?>
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

<a href="logout.php">Logout</a>

<?php require_once("footer.inc.php"); ?>
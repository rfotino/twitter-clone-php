<?php
if (!defined("WEBPAGE_CONTEXT")) {
    header("index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?php echo isset($page_title) ? $page_title : $DEFAULT_PAGE_TITLE; ?></title>
	<?php
	foreach ($CSS_FILES as $css_file) {
	    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"css/$css_file\" />\n";
	}
	foreach ($JS_FILES as $js_file) {
	    echo "<script type=\"text/javascript\" src=\"js/$js_file\"></script>\n";
	}
	?>
    </head>
    <body<?php if (isset($ONLOAD)) { echo " onload=\"$ONLOAD\""; } ?>>
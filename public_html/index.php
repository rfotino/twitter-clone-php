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

<div class="box">
    <p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vivamus eleifend urna in aliquam sollicitudin. Phasellus eu luctus tortor. Sed aliquet lectus ac rhoncus blandit. Quisque convallis neque eget orci placerat aliquet. Quisque pulvinar tortor a eleifend commodo. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ornare leo vitae magna dignissim condimentum. Donec et volutpat est. Fusce nunc augue, convallis luctus dolor eu, pretium tristique enim. Sed eu enim mauris. Aliquam erat volutpat.</p>
    <p>Sed quam eros, posuere nec rutrum eget, congue eleifend dui. Donec ultricies arcu vel risus tempor, eu pretium tellus commodo. Integer scelerisque mi eu mauris accumsan, a varius sapien accumsan. Praesent tempor laoreet enim, vitae varius ipsum ullamcorper in. Pellentesque quam sem, aliquam vel adipiscing vel, aliquet in est. Praesent iaculis sem quis nulla hendrerit, eget congue elit convallis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Etiam interdum, ipsum sit amet iaculis lacinia, tortor tortor blandit dolor, vitae aliquet metus lorem et sem. Morbi porttitor enim vel laoreet porttitor.</p>
</div>

<?php require_once("footer.inc.php"); ?>
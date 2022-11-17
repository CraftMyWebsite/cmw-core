<?php 
use CMW\Utils\View;

include_once("includes/head.inc.php");

/* INCLUDE SCRIPTS / STYLES*/
/* @var $includes */
View::loadInclude($includes, "beforeScript");
View::loadInclude($includes, "styles");

include_once("includes/sidebar.inc.php");
include_once("includes/header.inc.php");

echo $content;

include_once("includes/footer.inc.php");

/* INCLUDE SCRIPTS */
View::loadInclude($includes, "afterScript");

(isset($scripts) && !empty($scripts)) ? $scripts : "";
(isset($toaster) && !empty($toaster)) ? $toaster : "";
?>

</body>
</html>
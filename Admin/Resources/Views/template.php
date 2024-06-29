<?php

use CMW\Manager\Views\View;

include_once("Includes/head.inc.php");

/* INCLUDE SCRIPTS / STYLES*/
/* @var $includes */
/* @var $content */
View::loadInclude($includes, "beforeScript");
View::loadInclude($includes, "styles");

include_once("Includes/header.inc.php");

echo $content;

include_once("Includes/footer.inc.php");

/* INCLUDE SCRIPTS */
View::loadInclude($includes, "afterScript");
?>

</body>
</html>
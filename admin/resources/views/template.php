<?php 
use CMW\Utils\View;

include_once("includes/head.inc.php");
View::loadInclude($includes, "beforeScript");
View::loadInclude($includes, "styles");
include_once("includes/sidebar.inc.php");
include_once("includes/header.inc.php");
echo $content;
include_once("includes/footer.inc.php");
View::loadInclude($includes, "afterScript");
(isset($scripts) && !empty($scripts)) ? $scripts : "";
(isset($toaster) && !empty($toaster)) ? $toaster : "";
<?php use CMW\Manager\Views\View;

include_once("Includes/head.inc.php");
include_once("Includes/header.inc.php");


/* INCLUDE SCRIPTS / STYLES*/
/* @var $includes */
View::loadInclude($includes, "beforeScript");
View::loadInclude($includes, "styles");
?>

<?= /* @var string $content */ $content ?>

<?php View::loadInclude($includes, "afterScript"); ?>

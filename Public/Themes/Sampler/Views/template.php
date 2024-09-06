<?php
use CMW\Manager\Views\View;

include_once ('Includes/head.inc.php');
include_once ('Includes/header.inc.php');

/* INCLUDE SCRIPTS / STYLES */
/* @var $includes */
?>

<?= /* @var string $content */ $content ?>

<?php View::loadInclude($includes, 'afterScript', 'afterPhp'); ?>

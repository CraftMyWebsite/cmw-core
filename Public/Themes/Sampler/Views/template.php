<?php

use CMW\Controller\Core\PackageController;
use CMW\Manager\Views\View;

/* INCLUDE SCRIPTS / STYLES */
/* @var $includes */
View::loadInclude($includes, 'beforePhp');

include_once ('Includes/head.inc.php');
include_once ('Includes/header.inc.php');
?>

<?= /* @var string $content */ $content ?>

<?php
include_once ('Includes/footer.inc.php');
?>

<?php
if (PackageController::isInstalled('SimpleCookies')) {
    CMW\Controller\Simplecookies\SimpleCookiesController::getInstance()->showCookieConsent();
}
?>

<?php
View::loadInclude($includes, 'afterScript', 'afterPhp');
?>
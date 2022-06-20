<?php
global $_UTILS;

$packageFolder = 'app/package/';
$scannedDirectory = array_diff(scandir($packageFolder), array('..', '.'));
$dir = $_UTILS::getEnv()->getValue("dir");

foreach ($scannedDirectory as $package) {
    require($dir . "app/package/$package/routes.php");
}
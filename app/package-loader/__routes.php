<?php

use CMW\Utils\Utils;

$packageFolder = 'app/package/';
$scannedDirectory = array_diff(scandir($packageFolder), array('..', '.'));
$dir = Utils::getEnv()->getValue("dir");

foreach ($scannedDirectory as $package) {
    require($dir . "app/package/$package/routes.php");
}
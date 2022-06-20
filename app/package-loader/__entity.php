<?php
global $_UTILS;

$packageFolder = 'app/package/';
$scannedDirectory = array_diff(scandir($packageFolder), array('..', '.'));
$dir = $_UTILS::getEnv()->getValue("dir");

foreach ($scannedDirectory as $package) {
    $packageSubFolder = "app/package/$package/entities/";
    if (is_dir($packageSubFolder)) {
        $scannedSubDirectory = array_diff(scandir($packageSubFolder), array('..', '.'));
        foreach ($scannedSubDirectory as $entity) {
            require($dir . "app/package/$package/entities/$entity");
        }
    }
}
<?php
global $_UTILS;

$packageFolder = 'app/package/';
$scannedDirectory = array_diff(scandir($packageFolder), array('..', '.'));

foreach ($scannedDirectory as $package) {
    $packageSubFolder = "app/package/$package/models/";
    $scannedSubDirectory = array_diff(scandir($packageSubFolder), array('..', '.'));
    $dir = $_UTILS::getEnv()->getValue("dir");
    foreach ($scannedSubDirectory as $model) {
        require($dir . "app/package/$package/models/$model");
    }
}
<?php
global $_UTILS;

$packageFolder = 'app/package/';
$scannedDirectory = array_diff(scandir($packageFolder), array('..', '.'));
$dir = $_UTILS::getEnv()->getValue("dir");

foreach ($scannedDirectory as $package) {
    $packageSubFolder = "app/package/$package/functions/";
    if (is_dir($packageSubFolder)) {
        $scannedSubDirectory = array_diff(scandir($packageSubFolder), array('..', '.'));
        foreach ($scannedSubDirectory as $function) {
            require_once($dir . "app/package/$package/functions/$function");
        }
    }
}
<?php
global $_UTILS;

$packageFolder = 'app/package/';
$scanned_directory = array_diff(scandir($packageFolder), array('..', '.', '.model'));
$dir = $_UTILS::getEnv()->getValue("dir");

foreach ($scanned_directory as $package) {
    $package_subfolder = "app/package/$package/controllers/";
    $scanned_subdirectory = array_diff(scandir($package_subfolder), array('..', '.'));
    foreach ($scanned_subdirectory as $controller) {
        require($dir . "app/package/$package/controllers/$controller");
    }
}
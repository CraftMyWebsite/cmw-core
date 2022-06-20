<?php

$packageFolder = 'app/package/';
$scannedDirectory = array_diff(scandir($packageFolder), array('..', '.'));

foreach ($scannedDirectory as $package) {
    $packageSubFolder = "app/package/$package/models/";
    $scannedSubDirectory = array_diff(scandir($packageSubFolder), array('..', '.'));
    foreach ($scannedSubDirectory as $model) {
        require("package/$package/models/$model");
    }
}
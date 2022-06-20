<?php

$packageFolder = 'app/package/';
$scannedDirectory = array_diff(scandir($packageFolder), array('..', '.'));

foreach ($scannedDirectory as $package) {
    $packageSubFolder = "app/package/$package/entities/";
    if (is_dir($packageSubFolder)) {
        $scannedSubDirectory = array_diff(scandir($packageSubFolder), array('..', '.'));
        foreach ($scannedSubDirectory as $entity) {
            require("package/$package/entities/$entity");
        }
    }
}
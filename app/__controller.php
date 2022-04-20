<?php

$packageFolder = 'app/package/';
$scanned_directory = array_diff(scandir($packageFolder), array('..', '.', '.model'));

require("package/core/controllers/coreController.php");

foreach ($scanned_directory as $package) :
    if($package != 'core') :
        $package_subfolder = "app/package/$package/controllers/";
        $scanned_subdirectory = array_diff(scandir($package_subfolder), array('..', '.'));
        foreach ($scanned_subdirectory as $controller) :
            require("package/$package/controllers/$controller");
        endforeach;
    endif;
endforeach;
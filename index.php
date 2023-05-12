<?php

/*
 * Warning : This file must NOT be modified !
 */

use CMW\Manager\Loader\Loader;

require_once("App/Manager/Loader/Loader.php");

Loader::loadProject();

try {
    Loader::loadAttributes();
} catch (ReflectionException $e) {
    //TODO Errors
}

Loader::loadRoutes();

Loader::setLocale();

try {
    Loader::manageErrors();
} catch (ReflectionException $e) {
    //TODO Errors
}

Loader::loadInstall();

Loader::listenRouter();

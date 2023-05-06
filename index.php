<?php

/*
 * Warning : This file must NOT be modified !
 */

use CMW\Utils\Loader;

require_once("App/Utils/Loader.php");

Loader::loadProject();

Loader::loadAttributes();

Loader::loadRoutes();

Loader::setLocale();

Loader::manageErrors();

Loader::loadInstall();

Loader::listenRouter();

<?php

/*
 * Warning : This file must NOT be modified !
 */

use CMW\Utils\Loader;

require_once("app/tools/Utils.php");
require_once("app/tools/Loader.php");

session_start();

$loader = new Loader();

Loader::loadProject();

$router = $loader->getRouterInstance();

$loader->loadRoutes();

$loader->setLocale();

$loader->loadLangFiles(); //Todo remove that

$loader->manageErrors();

$loader->installManager();

$loader->listenRouter();

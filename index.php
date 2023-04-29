<?php

/*
 * Warning : This file must NOT be modified !
 */

use CMW\Manager\Error\ErrorManager;
use CMW\Utils\Loader;

require_once("App/Tools/Loader.php");

$loader = new Loader();

Loader::loadProject();

$router = Loader::getRouterInstance();

$loader->loadRoutes();

$loader->setLocale();

$loader->manageErrors(ErrorManager::class);

$loader->installManager();

$loader->listenRouter();


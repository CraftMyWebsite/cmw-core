<?php

/*
 * Warning : This file must NOT be modified !
 */

use CMW\Manager\Error\ErrorManager;
use CMW\Router\Router;
use CMW\Utils\Loader;

require_once("App/Tools/Loader.php");

$loader = new Loader();

Loader::loadProject();

$router = Router::getInstance();

$loader->loadAttributes();

$loader->loadRoutes();

$loader->setLocale();

$loader->manageErrors(ErrorManager::class);

$loader->installManager();

$loader->listenRouter();


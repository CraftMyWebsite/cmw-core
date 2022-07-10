<?php

/*
 * Warning : This file must NOT be modified !
 */

use CMW\Utils\Loader;
use CMW\Utils\Utils;

require_once("app/tools/Utils.php");
require_once("app/tools/Loader.php");

session_start();

$_UTILS = new Utils();
$loader = new Loader($_UTILS);

$_UTILS::getEnv()->addValue("locale", "fr");
date_default_timezone_set($_UTILS::getEnv()->getValue("TIMEZONE") ?? "UTC");

/*=> Load Router */
$router = $loader->loadRouter();

/*=> Load Packages */
$loader->loadPackages();

/*=> Load Global Constants */
$loader->loadGlobalConstants(); //TODO MODIF THAT

/*=> Manage Errors (DevMode) */
$loader->manageErrors();

/*=> Manage Installation part */
$loader->installManager();

/*=> Listen Router */
$loader->listenRouter();

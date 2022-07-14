<?php

/*
 * Warning : This file must NOT be modified !
 */

use CMW\Utils\Loader;

require_once("app/tools/Utils.php");
require_once("app/tools/Loader.php");

session_start();

$loader = new Loader();


/*=> Load Router */
$router = $loader->loadRouter();

/*=> Set Locale Timezone */
$loader->setLocale();

/*=> Load Packages */
$loader->loadPackages();

/*=> Load Tools */
$loader->loadTools();

/*=> Load Global Constants */
$loader->loadGlobalConstants(); //TODO MODIF THAT

/*=> Manage Errors (DevMode) */
$loader->manageErrors();

/*=> Manage Installation part */
$loader->installManager();

/*=> Listen Router */
$loader->listenRouter();

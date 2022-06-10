<?php

/*
 * Warning : This file must NOT be modified !
 */

use CMW\Utils\Utils;

session_start();

require_once("app/tools/Utils.php");
$_UTILS = new Utils();

$_UTILS::getEnv()->setOrEditValue("locale", $_UTILS::getEnv()->getValue("locale") ?: "fr");
date_default_timezone_set($_UTILS::getEnv()->getValue("TIMEZONE") ?: "UTC");

/*=> Load Router */
$router = $_UTILS->loadRouter();

/*=> Load Packages */
$_UTILS->loadPackages();


/*=> Load Global Constants */
$_UTILS->loadGlobalConstants(); //TODO MODIF THAT


/*=> Manage Errors (DevMode) */
$_UTILS->manageErrors();

/*=> Manage Installation part */
$_UTILS->installManager();

/*=> Listen Router */
$_UTILS->listenRouter();

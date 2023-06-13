<?php

/*
 * Warning : This file must NOT be modified !
 */

use CMW\Manager\Class\ClassManager;
use CMW\Manager\Loader\Loader;

require_once("App/Manager/Loader/Loader.php");

Loader::loadProject();

Loader::manageErrors();

Loader::loadAttributes();

Loader::loadRoutes();

Loader::setLocale();

Loader::loadInstall();

Loader::listenRouter();

print(ClassManager::getClassFullNameFromFile('App/Package/Core/Controllers/CoreController.php'));
<?php

/**
 * <p>Bootstrap file for the CMW application</p>
 * <p>This file is responsible for initializing the application, loading configurations, and setting up the environment for standalone env.</p>
 * <p><b>With this file, we don't load CMW routes / router.</b></p>
 */

use CMW\Manager\Loader\Loader;

// Set the environment to standalone
$GLOBALS['CMW_ENV'] = 'standalone';

// Set error reporting level
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);

// Standard CMW loading process
require_once('App/Manager/Loader/Loader.php');
Loader::loadProject();
Loader::manageErrors();
Loader::loadAttributes();
Loader::setLocale();
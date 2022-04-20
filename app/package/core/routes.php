<?php

use CMW\Router\router;

require_once('Lang/'.getenv("LOCALE").'.php');

/** @var $router router Main router */

/* Basics pages of CMS */
$router->get('/',"core#frontHome");

/* Administration scope of package */
$router->scope('/cmw-admin', function($router) {
    $router->get('/dashboard', "core#adminDashboard");
});
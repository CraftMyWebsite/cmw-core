<?php

use CMW\Router\router;
//Todo Try to remove that...
require_once('lang/'.getenv("LOCALE").'.php');

/** @var $router router Main router */

/* Administration scope of package */
$router->scope('/cmw-admin', function($router) {
    $router->get('/dashboard', "core#adminDashboard");

    $router->get('/configuration', "core#adminConfiguration");
    $router->post('/configuration', "core#adminConfigurationPost");
});

/* Basics pages of CMS (PUBLIC) */
$router->get('/',"core#frontHome");

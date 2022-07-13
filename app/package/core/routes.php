<?php

global $router;

use CMW\Router\Router;

/* Administration scope of package */
$router->scope('/cmw-admin', function(Router $router) {
    $router->get('/', "core#adminDashboard");
    $router->get('/dashboard', "core#adminDashboard");

    $router->get('/configuration', "core#adminConfiguration");
    $router->post('/configuration', "core#adminConfigurationPost");
});

/* Basics pages of CMS (PUBLIC) */
$router->get('/',"core#frontHome");

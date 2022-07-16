<?php

global $router;

use CMW\Controller\CoreController;
use CMW\Router\Router;
use CMW\Router\RouterException;

/* Administration scope of package */
$router->scope('/cmw-admin', function (Router $router) {
    $router->get('/', "core#adminDashboard");
    $router->get('/dashboard', "core#adminDashboard");

    $router->get('/configuration', "core#adminConfiguration");
    $router->post('/configuration', "core#adminConfigurationPost");
});

/* Error Manager */
$router->scope("geterror", function (Router $router) {

    $router->get("/:errorCode", function ($errorCode) {
        $coreController = new CoreController();
        $coreController->errorView($errorCode);
    });

});

$router->scope("error", function (Router $router) {

    $router->get("/:errorCode",
        /**
         * @throws \CMW\Router\RouterException
         */
        function ($errorCode) {
            throw new RouterException('Trowed Error', $errorCode);
        });

});

/* Basics pages of CMS (PUBLIC) */
$router->get('/', "core#frontHome");

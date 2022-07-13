<?php

global $router;

//Todo Try to remove that...
use CMW\Router\Router;

/* Administration scope of package */
$router->scope('/cmw-admin/menus', function(Router $router) {
    $router->get('/', "menus#adminMenus");
});
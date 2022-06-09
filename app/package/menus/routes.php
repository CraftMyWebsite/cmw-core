<?php

use CMW\Router\router;
//Todo Try to remove that...
require_once('Lang/'.getenv("LOCALE").'.php');

/** @var $router router Main router */


/* Administration scope of package */
$router->scope('/cmw-admin/menus', function($router) {
    $router->get('/', "menus#adminMenus");
});
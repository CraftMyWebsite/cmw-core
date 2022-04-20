<?php

use CMW\Router\router;

require_once('Lang/'.getenv("LOCALE").'.php');

/** @var $router router Main router */


/* Administration scope of package */
$router->scope('/cmw-admin/menus', function($router) {
    $router->get('/', "menus#adminMenus");
});
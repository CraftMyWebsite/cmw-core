<?php

global $router;

//Todo Try to remove that...
require_once('Lang/'.getenv("LOCALE").'.php');

/* Administration scope of package */
$router->scope('/cmw-admin/menus', function($router) {
    $router->get('/', "menus#adminMenus");
});
<?php

global $router;

use CMW\Controller\Roles\rolesController;
use CMW\Controller\Users\usersController;
use CMW\Router\router;

//Todo Try to remove that...
require_once('Lang/'.getenv("LOCALE").'.php');


/* Administration scope of package */
$router->scope('/cmw-admin', function(Router $router) {
    $router->get('/', "users#adminLogin");
    $router->post('/', "users#adminLoginPost");

    $router->get('/logout', "users#adminLogout");
});

$router->scope('/cmw-admin/users', function(Router $router) {
    $router->get('/list', "users#adminUsersList");

    $router->get('/edit/:id', function($id) {
        (new usersController)->adminUsersEdit($id);
    })->with('id', '[0-9]+');
    $router->post('/edit/:id', function($id) {
        (new usersController)->adminUsersEditPost($id);
    })->with('id', '[0-9]+');

    $router->get('/add', "users#adminUsersAdd");
    $router->post('/add', "users#adminUsersAddPost");

    $router->post('/edit-state', "users#adminUserState");
    $router->post('/delete', "users#adminUsersDelete");
});

$router->scope('/cmw-admin/roles', function(Router $router) {
    $router->get('/list', "roles#adminRolesList");

    $router->get('/add', "roles#adminRolesAdd");
    $router->post('/add', "roles#adminRolesAddPost");

    $router->get('/edit/:id', function($id) {
        //TODO need to try catch here.
        (new rolesController)->adminRolesEdit($id);
    })->with('id', '[0-9]+');
    $router->post('/edit/:id', function($id) {
        (new rolesController)->adminRolesEditPost($id);
    })->with('id', '[0-9]+');

    $router->get('/delete/:id', function($id) {
        (new rolesController())->adminRolesDelete($id);
    })->with('id', '[0-9]+');
});
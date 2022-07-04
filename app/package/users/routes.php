<?php

global $router;

use CMW\Controller\Roles\RolesController;
use CMW\Controller\Users\UsersController;
use CMW\Router\Router;

//Todo Try to remove that...
require_once('Lang/'.getenv("LOCALE").'.php');


/* Administration scope of package */
$router->scope('/cmw-admin', function(Router $router) {
    $router->get("/", "users#adminDashboard");
});

$router->scope('/cmw-admin/users', function(Router $router) {
    $router->get('/list', "users#adminUsersList");

    $router->get('/edit/:id', function($id) {
        (new UsersController)->adminUsersEdit($id);
    })->with('id', '[0-9]+');
    $router->post('/edit/:id', function($id) {
        (new UsersController)->adminUsersEditPost($id);
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
        (new RolesController)->adminRolesEdit($id);
    })->with('id', '[0-9]+');
    $router->post('/edit/:id', function($id) {
        (new RolesController)->adminRolesEditPost($id);
    })->with('id', '[0-9]+');

    $router->get('/delete/:id', function($id) {
        (new RolesController())->adminRolesDelete($id);
    })->with('id', '[0-9]+');
});

// PUBLIC

$router->scope('/', function(Router $router) {
    $router->get('/login', "users#login");
    $router->post('/login', "users#loginPost");

    $router->get('/logout', "users#logout");
});
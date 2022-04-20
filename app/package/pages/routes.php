<?php

use CMW\Controller\pages\pagesController;
use CMW\Router\router;

require_once('Lang/' . getenv("LOCALE") . '.php');

/** @var $router router Main router */

/* Fronts pages of CMS */

/* Administration scope of package */
$router->scope('/cmw-admin/pages', function ($router) {
    $router->get('/list', "pages#adminPagesList");

    $router->get('/edit/:id', function ($id) {
        (new pagesController)->adminPagesEdit($id);
    })->with('id', '[0-9]+');
    $router->post('/edit', "pages#adminPagesEditPost");

    $router->get('/add', "pages#adminPagesAdd");
    $router->post('/add', "pages#adminPagesAddPost");

    $router->post('/edit-state', "pages#adminUserState");
    $router->post('/delete', "pages#adminPagesDelete");
});


//Public pages
$router->scope('/p/', function ($router){

    $router->get('/:slug', function($slug) {
        (new pagesController)->publicShowPage($slug);
    })->with('slug', '.*?');

});

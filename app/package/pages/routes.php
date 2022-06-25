<?php

global $router;


use CMW\Controller\pages\pagesController;
use CMW\Router\router;

//Todo Try to remove that...
require_once('Lang/' . getenv("LOCALE") . '.php');


/* Fronts pages of CMS */

/* Administration scope of package */
$router->scope('/cmw-admin/pages', function ($router) {
    $router->get('/list', "pages#adminPagesList");

    $router->get('/edit/:slug', function ($slug) {
        (new pagesController)->adminPagesEdit($slug);
    })->with('slug', '.*?');
    $router->post('/edit', "pages#adminPagesEditPost");

    $router->get('/add', "pages#adminPagesAdd");
    $router->post('/add', "pages#adminPagesAddPost");

    $router->post('/edit-state', "pages#adminUserState");
    $router->post('/delete', "pages#adminPagesDelete");
});


//Public pages
$router->scope('/p', function ($router){

    $router->get('/:slug', function($slug) {
        (new pagesController)->publicShowPage($slug);
    })->with('slug', '.*?');

});

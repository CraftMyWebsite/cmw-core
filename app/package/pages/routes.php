<?php

global $router;


use CMW\Controller\pages\PagesController;
use CMW\Router\Router;

//Todo Try to remove that...

require_once('Lang/' . getenv("LOCALE") . '.php');

/* Fronts pages of CMS */

/* Administration scope of package */
$router->scope('/cmw-admin/pages', function (Router $router) {
    $router->get('/list', "pages#adminPagesList");

    $router->get('/edit/:slug', function (Router $slug) {
        (new PagesController)->adminPagesEdit($slug);
    })->with('slug', '.*?');

    $router->post('/edit', "pages#adminPagesEditPost");

    $router->getAndPost('/add', "pages#adminPagesAdd", "pages#adminPagesAddPost");

    $router->post('/edit-state', "pages#adminUserState");
    $router->post('/delete', "pages#adminPagesDelete");
});


//Public pages
$router->scope('/p', function (Router $router){

    $router->get('/:slug', function($slug) {
        (new PagesController)->publicShowPage($slug);
    })->with('slug', '.*?');

});

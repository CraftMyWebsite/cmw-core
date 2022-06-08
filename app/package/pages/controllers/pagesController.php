<?php

namespace CMW\Controller\pages;

use CMW\Controller\coreController;
use CMW\Controller\Menus\menusController;
use CMW\Controller\Users\usersController;
use CMW\Model\Pages\pagesModel;
use CMW\Model\Users\usersModel;

/**
 * Class: @pagesController
 * @package Pages
 * @author LoGuardiaN | <loguardian@hotmail.com>
 * @version 1.0
 */
class pagesController extends coreController
{
    public function adminPagesList()
    {
        usersController::isUserHasPermission("pages.show");

        $pagesModel = new pagesModel();
        $pagesList = $pagesModel->fetchAll();

        view('pages', 'list.admin', ["pagesList" => $pagesList], 'admin');
    }

    public function adminPagesAdd(): void
    {
        usersController::isUserHasPermission("pages.add");

        view('pages', 'add.admin', [], 'admin');
    }

    public function adminPagesAddPost(): void
    {
        usersController::isUserHasPermission("pages.add");

        $user = new usersModel();

        $page = new pagesModel();
        $page->pageTitle = filter_input(INPUT_POST, "news_title");
        $page->pageSlug = filter_input(INPUT_POST, "news_slug");
        $page->pageContent = filter_input(INPUT_POST, "news_content");
        $page->pageState = filter_input(INPUT_POST, "page_state");
        $page->userId = $user->getLoggedUser();

        var_dump($page->pageContent);

        $page->create();

        echo $page->pageId;
    }

    public function adminPagesEdit($id): void
    {
        usersController::isUserHasPermission("pages.edit");

        $page = new pagesModel();
        $page->pageId = $id;
        $page->fetch();

        $pageContent = $page->pageContent;

        view('pages', 'edit.admin', ["page"=> $page, "pageContent" => $pageContent], 'admin');
    }

    public function adminPagesEditPost(): void
    {
        usersController::isUserHasPermission("pages.edit");

        $page = new pagesModel();
        $page->pageId = filter_input(INPUT_POST, "news_id");
        $page->pageTitle = filter_input(INPUT_POST, "news_title");
        $page->pageSlug = filter_input(INPUT_POST, "news_slug");
        $page->pageContent = filter_input(INPUT_POST, "news_content");
        $page->pageState = filter_input(INPUT_POST, "page_state");

        $page->update();

        echo $page->pageId;
    }

    public function adminPagesDelete(): void
    {
        usersController::isUserHasPermission("pages.delete");

        $page = new pagesModel();
        $page->pageId = filter_input(INPUT_POST, "id");
        $page->delete();

        $_SESSION['toaster'][0]['title'] = CORE_TOASTER_TITLE;
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = CORE_TOASTER_DELETE_SUCCESS;

        header("location: ../pages/list");
        die();
    }



    /* Public section */
    public function publicShowPage($slug): void
    {

        //Default controllers (important)
        $core = new coreController();
        $menu = new menusController();

        $page = new pagesModel();

        $page->pageSlug = $slug;
        $page->fetch($page->pageSlug);
        $pageContent = $page->pageContent;


        //Include the public view file ("public/themes/$themePath/views/wiki/main.view.php")
        view('pages', 'main', ["page" => $page,"pageContent" => $pageContent,
            "slug" => $slug , "core" => $core, "menu" => $menu], 'public');
    }



}
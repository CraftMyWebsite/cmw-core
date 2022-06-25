<?php

namespace CMW\Controller\pages;

use CMW\Controller\coreController;
use CMW\Controller\Menus\menusController;
use CMW\Controller\Users\usersController;
use CMW\Entity\Pages\pagesEntity;
use CMW\Model\Pages\pagesModel;
use CMW\Model\Users\usersModel;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @pagesController
 * @package Pages
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class pagesController extends coreController
{

    private pagesModel $pagesModel;

    public function __construct($theme_path = null)
    {
        parent::__construct($theme_path);
        $this->pagesModel = new pagesModel();
    }

    public function adminPagesList(): void
    {
        usersController::isUserHasPermission("pages.show");

        $pages = new pagesModel();
        $users = new usersModel();

        view('pages', 'list.admin', ["pages" => $pages, "users" => $users], 'admin');
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
        $pageTitle = filter_input(INPUT_POST, "news_title");
        $pageSlug = filter_input(INPUT_POST, "news_slug");
        $pageContent = filter_input(INPUT_POST, "news_content");
        $pageState = filter_input(INPUT_POST, "page_state");
        $userId = $user::getLoggedUser();

        $page->create($pageSlug, $userId, $pageTitle, $pageContent, $pageState);
    }

    public function adminPagesEdit($slug): void
    {
        usersController::isUserHasPermission("pages.edit");

        $page = new pagesModel();
        $page->getPage($slug);

        view('pages', 'edit.admin', ["page" => $page, "slug" => $slug], 'admin');
    }

    public function adminPagesEditPost(): void
    {
        usersController::isUserHasPermission("pages.edit");

        $page = new pagesModel();
        $pageTitle = filter_input(INPUT_POST, "news_title");
        $pageSlug = filter_input(INPUT_POST, "news_slug");
        $pageContent = filter_input(INPUT_POST, "news_content");
        $pageState = filter_input(INPUT_POST, "page_state");
        $pageId = filter_input(INPUT_POST, "news_id");

        $page->update($pageSlug, $pageTitle, $pageContent, $pageState, $pageId);
    }

    #[NoReturn] public function adminPagesDelete(): void
    {
        usersController::isUserHasPermission("pages.delete");

        $page = new pagesModel();
        $pageId = filter_input(INPUT_POST, "id");
        $page->delete($pageId);

        //Todo try to remove that
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

        $page->getPage($slug);

        //Include the public view file ("public/themes/$themePath/views/pages/main.view.php")
        view('pages', 'main', ["page" => $page,
            "slug" => $slug, "core" => $core, "menu" => $menu], 'public');
    }


}
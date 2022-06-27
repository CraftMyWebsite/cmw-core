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

        $pagesModel = new pagesModel();
        $pagesList = $pagesModel->getPages();

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

        $pageEntity = $page->createPage($pageTitle, $pageSlug, $pageContent, $userId, $pageState);

        echo $pageEntity?->getId() ?? -1;

    }

    public function adminPagesEdit($slug): void
    {
        usersController::isUserHasPermission("pages.edit");

        $page = (new pagesModel())->getPageById($id);

        view('pages', 'edit.admin', ["page" => $page], 'admin');

    }

    public function adminPagesEditPost(): void
    {
        usersController::isUserHasPermission("pages.edit");

        $page = new pagesModel();
      
        $id = filter_input(INPUT_POST, "news_id");
        $title = filter_input(INPUT_POST, "news_title");
        $slug = filter_input(INPUT_POST, "news_slug");
        $content = filter_input(INPUT_POST, "news_content");
        $state = filter_input(INPUT_POST, "page_state");

        $pageEntity = $page->updatePage($id, $slug, $title, $content, $state);

        echo $pageEntity?->getId() ?? -1;
    }

    #[NoReturn] public function adminPagesDelete(): void
    {
        usersController::isUserHasPermission("pages.delete");

        $page = new pagesModel();
      
        $id = filter_input(INPUT_POST, "id");
        $page->deletePage($id);


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

        $pageEntity = $page->getPageBySlug($slug);

        //Include the public view file ("public/themes/$themePath/views/wiki/main.view.php")
        view('pages', 'main', ["pages" => $page, "page" => $pageEntity, "core" => $core, "menu" => $menu], 'public');

    }


}
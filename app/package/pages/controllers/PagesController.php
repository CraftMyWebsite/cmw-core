<?php

namespace CMW\Controller\pages;

use CMW\Controller\CoreController;
use CMW\Controller\Menus\MenusController;
use CMW\Controller\Users\UsersController;
use CMW\Entity\Pages\pagesEntity;
use CMW\Model\Pages\PagesModel;
use CMW\Model\Users\UsersModel;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @pagesController
 * @package Pages
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class PagesController extends CoreController
{

    private PagesModel $pagesModel;

    public function __construct($theme_path = null)
    {
        parent::__construct($theme_path);
        $this->pagesModel = new PagesModel();
    }

    public function adminPagesList(): void
    {
        UsersController::isUserHasPermission("pages.show");

        $pagesList = $this->pagesModel->getPages();

        view('pages', 'list.admin', ["pagesList" => $pagesList], 'admin');
    }

    public function adminPagesAdd(): void
    {
        UsersController::isUserHasPermission("pages.add");

        view('pages', 'add.admin', [], 'admin');
    }

    public function adminPagesAddPost(): void
    {
        UsersController::isUserHasPermission("pages.add");

        $user = new UsersModel();

        $page = new PagesModel();
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
        UsersController::isUserHasPermission("pages.edit");

        $page = $this->pagesModel->getPageBySlug($slug);

        view('pages', 'edit.admin', ["page" => $page], 'admin');

    }

    public function adminPagesEditPost(): void
    {
        UsersController::isUserHasPermission("pages.edit");

        $page = new PagesModel();
      
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
        UsersController::isUserHasPermission("pages.delete");
      
        $id = filter_input(INPUT_POST, "id");
        $this->pagesModel->deletePage($id);


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
        $core = new CoreController();
        $menu = new MenusController();
        $page = new PagesModel();

        $pageEntity = $page->getPageBySlug($slug);

        //Include the public view file ("public/themes/$themePath/views/pages/main.view.php")
        view('pages', 'main', ["pages" => $page, "page" => $pageEntity, "core" => $core, "menu" => $menu], 'public');

    }


}

<?php

namespace CMW\Controller\pages;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Users\UsersController;
use CMW\Model\Pages\PagesModel;
use CMW\Model\Users\UsersModel;
use CMW\Router\Link;
use CMW\Router\LinkStorage;
use CMW\Utils\Utils;
use CMW\Utils\View;
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

    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin/pages")]
    #[Link("/list", Link::GET, [], "/cmw-admin/pages")]
    public function adminPagesList(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "pages.show");

        $pagesList = $this->pagesModel->getPages();

        View::createAdminView('pages', 'list')
            ->addStyle("admin/resources/vendors/simple-datatables/style.css","admin/resources/assets/css/pages/simple-datatables.css")
            ->addScriptAfter("admin/resources/vendors/simple-datatables/umd/simple-datatables.js","admin/resources/assets/js/pages/simple-datatables.js")
            ->addVariableList(["pagesList" => $pagesList])
            ->view();
    }

    #[Link("/add", Link::GET, [], "/cmw-admin/pages")]
    public function adminPagesAdd(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "pages.add");

        View::createAdminView('pages', 'add')
            ->addScriptBefore("admin/resources/vendors/editorjs/plugins/header.js",
                "admin/resources/vendors/editorjs/plugins/image.js",
                "admin/resources/vendors/editorjs/plugins/delimiter.js",
                "admin/resources/vendors/editorjs/plugins/list.js",
                "admin/resources/vendors/editorjs/plugins/quote.js",
                "admin/resources/vendors/editorjs/plugins/code.js",
                "admin/resources/vendors/editorjs/plugins/table.js",
                "admin/resources/vendors/editorjs/plugins/link.js",
                "admin/resources/vendors/editorjs/plugins/warning.js",
                "admin/resources/vendors/editorjs/plugins/embed.js",
                "admin/resources/vendors/editorjs/plugins/marker.js",
                "admin/resources/vendors/editorjs/plugins/underline.js",
                "admin/resources/vendors/editorjs/plugins/drag-drop.js",
                "admin/resources/vendors/editorjs/plugins/undo.js",
                "admin/resources/vendors/editorjs/editor.js")
            ->addScriptAfter("admin/resources/vendors/jquery/jquery.min.js")
            ->view();
    }

    #[Link("/add", Link::POST, [], "/cmw-admin/pages", secure: false)]
    public function adminPagesAddPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "pages.add");

        $user = new UsersModel();

        $page = new PagesModel();
        $pageTitle = filter_input(INPUT_POST, "news_title");
        $pageSlug = Utils::normalizeForSlug(filter_input(INPUT_POST, "news_slug"));
        $pageContent = filter_input(INPUT_POST, "news_content");
        $pageState = filter_input(INPUT_POST, "page_state");
        $userId = $user::getLoggedUser();

        $pageEntity = $page->createPage($pageTitle, $pageSlug, $pageContent, $userId, $pageState);

        //Add route
        (new LinkStorage())->storeRoute('p/' . $pageSlug, 'page', 'Page | ' . $pageTitle, 'GET',
            'false', 'false', 1);

        echo $pageEntity?->getId() ?? -1;
    }

    #[Link("/edit/:slug", Link::GET, ["slug" => ".*?"], "/cmw-admin/pages")]
    public function adminPagesEdit(string $slug): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "pages.edit");

        $page = $this->pagesModel->getPageBySlug($slug);

        View::createAdminView('pages', 'edit')
            ->addStyle("admin/resources/vendors/summernote/summernote-lite.css","admin/resources/assets/css/pages/summernote.css")
            ->addScriptAfter("admin/resources/vendors/jquery/jquery.min.js","admin/resources/vendors/summernote/summernote-lite.min.js","admin/resources/assets/js/pages/summernote.js")
            ->addVariableList(["page" => $page])
            ->view();
    }

    #[Link("/edit", Link::POST, [], "/cmw-admin/pages", secure: false)]
    public function adminPagesEditPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "pages.edit");

        $page = new PagesModel();
      
        $id = filter_input(INPUT_POST, "news_id");
        $title = filter_input(INPUT_POST, "news_title");
        $slug = filter_input(INPUT_POST, "news_slug");
        $content = filter_input(INPUT_POST, "news_content");
        $state = filter_input(INPUT_POST, "page_state");

        $pageEntity = $page->updatePage($id, $slug, $title, $content, $state);

        echo $pageEntity?->getId() ?? -1;
    }

    #[Link("/delete/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/pages")]
    #[NoReturn] public function adminPagesDelete(int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "pages.delete");

        $this->pagesModel->deletePage($id);

        //Todo try to remove that
        $_SESSION['toaster'][0]['title'] = "CORE_TOASTER_TITLE";
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = "CORE_TOASTER_DELETE_SUCCESS";

        header("location: ../list");
    }


    /* Public section */
    /**
     * @throws \CMW\Router\RouterException
     */
    #[Link('/p/:slug', Link::GET, ["slug" => ".*?"])]
    public function publicShowPage(string $slug): void
    {

        //Default controllers (important)
        $page = new PagesModel();

        $pageEntity = $page->getPageBySlug($slug);

        //Include the public view file ("public/themes/$themePath/views/pages/main.view.php")
        $view = new View('pages', 'main');
        $view->addVariableList( ["pages" => $page, "page" => $pageEntity]);
        $view->view();
    }


}

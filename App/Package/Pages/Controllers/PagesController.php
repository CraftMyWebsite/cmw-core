<?php

namespace CMW\Controller\pages;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Core\EditorController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Model\Pages\PagesModel;
use CMW\Model\Users\UsersModel;
use CMW\Router\Link;
use CMW\Router\LinkStorage;
use CMW\Utils\Utils;
use CMW\Manager\Views\View;
use JetBrains\PhpStorm\NoReturn;
use CMW\Utils\Response;
use JsonException;

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

    #[Link(path: "/", method: Link::GET, scope: "/cmw-Admin/pages")]
    #[Link("/list", Link::GET, [], "/cmw-Admin/pages")]
    public function adminPagesList(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "pages.show");

        $pagesList = $this->pagesModel->getPages();

        View::createAdminView('pages', 'list')
            ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css","Admin/Resources/Assets/Css/Pages/simple-datatables.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/Umd/simple-datatables.js","Admin/Resources/Assets/Js/Pages/simple-datatables.js")
            ->addVariableList(["pagesList" => $pagesList])
            ->view();
    }

    #[Link("/add", Link::GET, [], "/cmw-Admin/pages")]
    public function adminPagesAdd(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "pages.add");

        View::createAdminView('pages', 'add')
            ->addScriptBefore("Admin/Resources/Vendors/editorjs/plugins/header.js",
                "Admin/Resources/Vendors/editorjs/plugins/image.js",
                "Admin/Resources/Vendors/editorjs/plugins/delimiter.js",
                "Admin/Resources/Vendors/editorjs/plugins/list.js",
                "Admin/Resources/Vendors/editorjs/plugins/quote.js",
                "Admin/Resources/Vendors/editorjs/plugins/code.js",
                "Admin/Resources/Vendors/editorjs/plugins/table.js",
                "Admin/Resources/Vendors/editorjs/plugins/link.js",
                "Admin/Resources/Vendors/editorjs/plugins/warning.js",
                "Admin/Resources/Vendors/editorjs/plugins/embed.js",
                "Admin/Resources/Vendors/editorjs/plugins/marker.js",
                "Admin/Resources/Vendors/editorjs/plugins/underline.js",
                "Admin/Resources/Vendors/editorjs/plugins/drag-drop.js",
                "Admin/Resources/Vendors/editorjs/plugins/undo.js",
                "Admin/Resources/Vendors/editorjs/editor.js")
            ->view();
    }

    #[Link("/add", Link::POST, [], "/cmw-Admin/pages", secure: false)]
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

    #[Link("/edit/:slug", Link::GET, ["slug" => ".*?"], "/cmw-Admin/pages")]
    public function adminPagesEdit(string $slug): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "pages.edit");

        $page = $this->pagesModel->getPageBySlug($slug);

        View::createAdminView('pages', 'edit')
            ->addScriptBefore("Admin/Resources/Vendors/editorjs/plugins/header.js",
                "Admin/Resources/Vendors/editorjs/plugins/image.js",
                "Admin/Resources/Vendors/editorjs/plugins/delimiter.js",
                "Admin/Resources/Vendors/editorjs/plugins/list.js",
                "Admin/Resources/Vendors/editorjs/plugins/quote.js",
                "Admin/Resources/Vendors/editorjs/plugins/code.js",
                "Admin/Resources/Vendors/editorjs/plugins/table.js",
                "Admin/Resources/Vendors/editorjs/plugins/link.js",
                "Admin/Resources/Vendors/editorjs/plugins/warning.js",
                "Admin/Resources/Vendors/editorjs/plugins/embed.js",
                "Admin/Resources/Vendors/editorjs/plugins/marker.js",
                "Admin/Resources/Vendors/editorjs/plugins/underline.js",
                "Admin/Resources/Vendors/editorjs/plugins/drag-drop.js",
                "Admin/Resources/Vendors/editorjs/plugins/undo.js",
                "Admin/Resources/Vendors/editorjs/editor.js")
            ->addVariableList(["page" => $page])
            ->view();
    }

    #[Link("/edit", Link::POST, [], "/cmw-Admin/pages", secure: false)]
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

    #[Link("/delete/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-Admin/pages")]
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

    /**
     * @param string $type => add, edit
     * @return void
     */
    #[Link("/uploadImage/:type", Link::POST, ["type" => ".*?"], "/cmw-Admin/pages", secure: false)]
    public function adminPagesUploadImagePost(string $type): void
    {

        if ($type === "add") {
            UsersController::hasPermission("core.dashboard", "pages.add");
        } else {
            UsersController::hasPermission("core.dashboard", "pages.edit");
        }


        try {
            print(json_encode(ImagesManager::upload($_FILES['image'], "editor"), JSON_THROW_ON_ERROR));
        } catch (JsonException $e) {
            echo $e;
        }

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

        //Include the Public view file ("Public/Themes/$themePath/Views/Pages/main.view.php")
        $view = new View('pages', 'main');
        $view->addScriptBefore("Admin/Resources/Vendors/highlight/highlight.min.js","Admin/Resources/Vendors/highlight/highlightAll.js");
        $view->addStyle("Admin/Resources/Vendors/highlight/style/" . EditorController::getCurrentStyle());
        $view->addVariableList( ["pages" => $page, "page" => $pageEntity]);
        $view->view();
        
    }


}

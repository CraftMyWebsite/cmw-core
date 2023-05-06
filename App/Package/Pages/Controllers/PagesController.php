<?php

namespace CMW\Controller\pages;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Core\EditorController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Router\LinkStorage;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Manager\Views\View;
use CMW\Model\Pages\PagesModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;
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

    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin/pages")]
    #[Link("/list", Link::GET, [], "/cmw-admin/pages")]
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

    #[Link("/add", Link::GET, [], "/cmw-admin/pages")]
    public function adminPagesAdd(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "pages.add");

        View::createAdminView('pages', 'add')
            ->addScriptBefore("Admin/Resources/Vendors/Editorjs/Plugins/header.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/image.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/delimiter.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/list.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/quote.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/code.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/table.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/link.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/warning.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/embed.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/marker.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/underline.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/drag-drop.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/undo.js",
                "Admin/Resources/Vendors/Editorjs/editor.js")
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
    public function adminPagesEdit(Request $request, string $slug): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "pages.edit");

        $page = $this->pagesModel->getPageBySlug($slug);

        View::createAdminView('pages', 'edit')
            ->addScriptBefore("Admin/Resources/Vendors/Editorjs/Plugins/header.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/image.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/delimiter.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/list.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/quote.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/code.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/table.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/link.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/warning.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/embed.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/marker.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/underline.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/drag-drop.js",
                "Admin/Resources/Vendors/Editorjs/Plugins/undo.js",
                "Admin/Resources/Vendors/Editorjs/editor.js")
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
    #[NoReturn] public function adminPagesDelete(Request $request, int $id): void
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
    #[Link("/uploadImage/:type", Link::POST, ["type" => ".*?"], "/cmw-admin/pages", secure: false)]
    public function adminPagesUploadImagePost(Request $request, string $type): void
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
     * @throws \CMW\Manager\Router\RouterException
     */
    #[Link('/p/:slug', Link::GET, ["slug" => ".*?"])]
    public function publicShowPage(Request $request, string $slug): void
    {

        //Default controllers (important)
        $page = new PagesModel();

        $pageEntity = $page->getPageBySlug($slug);

        //Include the Public view file ("Public/Themes/$themePath/Views/Pages/main.view.php")
        $view = new View('Pages', 'main');
        $view->addScriptBefore("Admin/Resources/Vendors/Highlight/highlight.min.js","Admin/Resources/Vendors/Highlight/highlightAll.js");
        $view->addStyle("Admin/Resources/Vendors/Highlight/Style/" . EditorController::getCurrentStyle());
        $view->addVariableList( ["pages" => $page, "page" => $pageEntity]);
        $view->view();
        
    }


}

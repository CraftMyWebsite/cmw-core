<?php

namespace CMW\Controller\Pages;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Error\ErrorManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Router\LinkStorage;
use CMW\Manager\Router\Router;
use CMW\Manager\Uploads\ImagesException;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Manager\Views\View;
use CMW\Model\Pages\PagesModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @pagesController
 * @package Pages
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class PagesController extends AbstractController
{
    #[Link('/', Link::GET, [], '/cmw-admin/pages')]
    private function adminPagesList(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'pages.show');

        $pagesList = PagesModel::getInstance()->getPages();

        View::createAdminView('Pages', 'page')
            ->addStyle('Admin/Resources/Assets/Css/simple-datatables.css')
            ->addScriptAfter('Admin/Resources/Vendors/Simple-datatables/simple-datatables.js', 'Admin/Resources/Vendors/Simple-datatables/config-datatables.js')
            ->addVariableList(['pagesList' => $pagesList])
            ->view();
    }

    #[Link('/add', Link::GET, [], '/cmw-admin/pages')]
    private function adminPagesAdd(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'pages.show.add');

        // Todo "pack script" to avoid that
        View::createAdminView('Pages', 'add')
            ->addScriptBefore('Admin/Resources/Vendors/Tinymce/tinymce.min.js',
                'Admin/Resources/Vendors/Tinymce/Config/full.js')
            ->view();
    }

    #[Link('/add', Link::POST, [], '/cmw-admin/pages', secure: false)]
    private function adminPagesAddPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'pages.show.add');

        $userId = UsersModel::getCurrentUser()?->getId();
        $slug = Utils::normalizeForSlug(filter_input(INPUT_POST, 'page_slug'));

        [$title, $content, $state] = Utils::filterInput('title', 'content', 'state');

        PagesModel::getInstance()->createPage($title, $slug, $content, $userId, $state === NULL ? 0 : 1);

        // Add route
        LinkStorage::getInstance()->storeRoute('p/' . $slug, 'page', 'Page | ' . $title, 'GET',
            'false', 'false', 1);

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'), LangManager::translate('pages.alert.added'));

        Redirect::redirectPreviousRoute();
    }

    #[Link('/edit/:slug', Link::GET, ['slug' => '.*?'], '/cmw-admin/pages')]
    private function adminPagesEdit(string $slug): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'pages.show.edit');

        $page = PagesModel::getInstance()->getPageBySlug($slug);

        // Todo "pack script" to avoid that
        View::createAdminView('Pages', 'edit')
            ->addScriptBefore('Admin/Resources/Vendors/Tinymce/tinymce.min.js',
                'Admin/Resources/Vendors/Tinymce/Config/full.js')
            ->addVariableList(['page' => $page])
            ->view();
    }

    #[Link('/edit/:slug', Link::POST, [], '/cmw-admin/pages')]
    private function adminPagesEditPost(string $slug): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'pages.show.edit');

        [$id, $title, $content, $state] = Utils::filterInput('id', 'title', 'content', 'state');

        if (Utils::containsNullValue($id, $title, $content)) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('pages.toaster.errors.emptyFields'));
            Redirect::redirectPreviousRoute();
        }

        PagesModel::getInstance()->updatePage($id, $slug, $title, $content, $state === NULL ? 0 : 1);

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'), LangManager::translate('pages.alert.edited'));
        Redirect::redirectPreviousRoute();
    }

    #[Link('/delete/:id', Link::GET, ['id' => '[0-9]+'], '/cmw-admin/pages')]
    #[NoReturn]
    private function adminPagesDelete(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'pages.show.delete');

        PagesModel::getInstance()->deletePage($id);

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('pages.toaster.deleted'));

        Redirect::redirectPreviousRoute();
    }

    /**
     * @param string $type => add, edit
     * @return void
     * @throws \JsonException
     */
    #[Link('/uploadImage/:type', Link::POST, ['type' => '.*?'], '/cmw-admin/pages', secure: false)]
    private function adminPagesUploadImagePost(string $type): void
    {
        UsersController::hasPermission('core.dashboard', 'pages.show.' . (($type === 'add') ? 'add' : 'edit'));

        try {
            print (json_encode(ImagesManager::upload($_FILES['image'], 'Editor'), JSON_THROW_ON_ERROR));
        } catch (ImagesException $e) {
            echo $e;  // todo error
        }
    }

    /* Public section */

    /**
     * @throws \CMW\Manager\Router\RouterException
     */
    #[Link('/:slug', Link::GET, ['slug' => '.*?'], weight: 0)]
    private function publicShowPage(string $slug): void
    {
        $pageEntity = PagesModel::getInstance()->getPageBySlug($slug);

        // If page slug exist
        if (!is_null($pageEntity)) {
            // Include the Public view file ("Public/Themes/$themePath/Views/Pages/main.view.php")
            $view = new View('Pages', 'main');
            $view->addScriptBefore('Admin/Resources/Vendors/Prismjs/prism.js');
            $view->addVariableList(['page' => $pageEntity]);
            $view->view();
            return;
        }

        $route = Router::getInstance()->getRouteByUrl($slug);

        if (is_null($route) || $route->getName() === 'pages.publicShowPage') {
            ErrorManager::showError(404);
            die();
        }
    }
}

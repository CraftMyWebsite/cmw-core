<?php

namespace CMW\Controller\Pages;

use CMW\Controller\Users\UsersController;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Manager\Cache\SimpleCacheManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Error\ErrorManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Router\LinkStorage;
use CMW\Manager\Router\Router;
use CMW\Manager\Router\RouterException;
use CMW\Manager\Uploads\ImagesException;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Manager\Views\View;
use CMW\Manager\Xml\SitemapManager;
use CMW\Model\Pages\PagesModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;
use function is_null;
use function json_encode;
use const JSON_THROW_ON_ERROR;

/**
 * Class: @pagesController
 * @package Pages
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
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

        View::createAdminView('Pages', 'add')
            ->addScriptBefore('Admin/Resources/Vendors/Tinymce/tinymce.min.js',
                'Admin/Resources/Vendors/Tinymce/Config/full.js')
            ->addScriptAfter('App/Package/Pages/Views/Assets/Js/slugGenerator.js')
            ->view();
    }

    #[NoReturn] #[Link('/add', Link::POST, [], '/cmw-admin/pages')]
    private function adminPagesAddPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'pages.show.add');

        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();

        [$title, $content, $state, $slug] = Utils::filterInput('title', 'content', 'state', 'page_slug');

        if ($slug === '') {
            $slug = Utils::normalizeForSlug($title);
        } else {
            $slug = Utils::normalizeForSlug($slug);
        }

        PagesModel::getInstance()->createPage($title, $slug, $content, $userId, $state === NULL ? 0 : 1);

        // Add route
        LinkStorage::getInstance()->storeRoute('p/' . $slug, 'page', 'Page | ' . $title, 'GET',
            'false', 'false', 1);

        SitemapManager::getInstance()->add($slug, 0.75);

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'), LangManager::translate('pages.alert.added'));

        $this->clearSingleCachePage();

        Redirect::redirect('cmw-admin/pages');
    }

    #[Link('/edit/:slug', Link::GET, ['slug' => '.*?'], '/cmw-admin/pages')]
    private function adminPagesEdit(string $slug): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'pages.show.edit');

        $page = PagesModel::getInstance()->getPageBySlug($slug);

        if (is_null($page)) {
            Redirect::errorPage(404);
        }

        View::createAdminView('Pages', 'edit')
            ->addScriptBefore('Admin/Resources/Vendors/Tinymce/tinymce.min.js',
                'Admin/Resources/Vendors/Tinymce/Config/full.js')
            ->addScriptAfter('App/Package/Pages/Views/Assets/Js/slugGenerator.js')
            ->addVariableList(['page' => $page])
            ->view();
    }

    #[NoReturn] #[Link('/edit/:slug', Link::POST, [], '/cmw-admin/pages')]
    private function adminPagesEditPost(string $slug): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'pages.show.edit');

        $page = PagesModel::getInstance()->getPageBySlug($slug);

        if (is_null($page)) {
            Redirect::errorPage(404);
        }

        [$id, $title, $content, $state, $updatedSlug] = Utils::filterInput('id', 'title', 'content', 'state', 'slug');

        if ($updatedSlug === '') {
            $updatedSlug = Utils::normalizeForSlug($title);
        } else {
            $updatedSlug = Utils::normalizeForSlug($updatedSlug);
        }

        if (Utils::containsNullValue($id, $title, $content)) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('pages.toaster.errors.emptyFields'));
            Redirect::redirectPreviousRoute();
        }

        $updatedPage = PagesModel::getInstance()->updatePage($id, $updatedSlug, $title, $content, $state === NULL ? 0 : 1);

        if (is_null($updatedPage)) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('pages.toaster.errors.update'));
            Redirect::redirectPreviousRoute();
        }

        //Update sitemap
        if (($page->getState() === 1 && $updatedPage->getState() === 1) && $updatedPage->getSlug() === $page->getSlug()) {
            SitemapManager::getInstance()->update($page->getSlug(), 0.75);
        } else if ($updatedPage->getState() === 1 && $page->getState() === 1) {
            SitemapManager::getInstance()->delete($page->getSlug());
            SitemapManager::getInstance()->add($updatedPage->getSlug(), 0.75);
        } else if ($updatedPage->getState() === 1 && $page->getState() !== 1) {
            SitemapManager::getInstance()->add($updatedPage->getSlug(), 0.75);
        } else if ($updatedPage->getState() !== 1) {
            SitemapManager::getInstance()->delete($page->getSlug());
        }

        $this->clearSingleCachePage($id, $slug);

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'), LangManager::translate('pages.alert.edited'));
        Redirect::redirectToAdmin('pages/edit/', ['slug' => $updatedSlug]);
    }

    #[Link('/delete/:id', Link::GET, ['id' => '[0-9]+'], '/cmw-admin/pages')]
    #[NoReturn]
    private function adminPagesDelete(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'pages.show.delete');

        $page = PagesModel::getInstance()->getPageById($id);

        if (is_null($page)) {
            Redirect::errorPage(404);
        }

        PagesModel::getInstance()->deletePage($id);

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('pages.toaster.deleted'));

        SitemapManager::getInstance()->delete($page->getSlug());

        $this->clearSingleCachePage($id, $page->getSlug());

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
            print (json_encode(ImagesManager::convertAndUpload($_FILES['image'], 'Editor'), JSON_THROW_ON_ERROR));
        } catch (ImagesException $e) {
            echo $e;  // todo error
        }
    }

    /* Public section */

    /**
     * @throws RouterException
     */
    #[Link('/:slug', Link::GET, ['slug' => '.*?'], weight: 0)]
    private function publicShowPage(string $slug): void
    {
        $pageEntity = PagesModel::getInstance()->getPageBySlug($slug);

        // If page slug exist
        if (!is_null($pageEntity)) {
            if ($pageEntity->getState() === 1 && !UsersController::isAdminLogged()) {
                Flash::send(Alert::INFO, 'Pages', 'Cette page n\'est pas encore publique !');
                Redirect::redirectToHome();
            } else {
                View::createPublicView('Pages', 'main')
                    ->addVariableList(['page' => $pageEntity])
                    ->addScriptBefore('Admin/Resources/Vendors/Prismjs/prism.js')
                    ->view();
            }
            return;
        }

        $route = Router::getInstance()->getRouteByUrl($slug);

        if (is_null($route) || $route->getName() === 'pages.publicShowPage') {
            ErrorManager::showError(404);
            die();
        }
    }

    /**
     * <p>Clear all pages cache files (App/Storage/Cache/Pages)</p>
     * @return void
     */
    private function clearPagesCache(): void
    {
        $dir = EnvManager::getInstance()->getValue('DIR') . 'App/Storage/Cache/Pages/';
        SimpleCacheManager::deleteAllFiles($dir);
    }

    /**
     * <p>Clear a specific page cache file (App/Storage/Cache/Pages)</p>
     * @param int|null $id
     * @param string|null $slug
     * @return void
     */
    private function clearSingleCachePage(?int $id = null, ?string $slug = null): void
    {
        if (!is_null($id)) {
            SimpleCacheManager::deleteSpecificCacheFile("page_id_$id", 'Pages');
        }

        if (!is_null($slug)) {
            SimpleCacheManager::deleteSpecificCacheFile("page_slug_$slug", 'Pages');
        }

        //Delete list of pages cache file
        SimpleCacheManager::deleteSpecificCacheFile('pages', 'Pages');
    }
}

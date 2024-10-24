<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Interface\Core\IMenus;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Loader\Loader;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Core\MenusModel;
use CMW\Model\Users\RolesModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @MenusController
 * @package Core
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 0.0.1
 */
class MenusController extends AbstractController
{
    #[Link(path: '/', method: Link::GET, scope: '/cmw-admin/menus')]
    #[Link('/', Link::GET, [], '/cmw-admin/menus')]
    private function adminMenus(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.menu');

        $packagesLinks = $this->getPackagesLinks();
        $roles = RolesModel::getInstance()->getRoles();
        $menus = MenusModel::getInstance();

        $view = View::createAdminView('Core', 'Menu/menus')
            ->addVariableList(['packagesLinks' => $packagesLinks, 'roles' => $roles, 'menus' => $menus])
            ->addScriptBefore('App/Package/Core/Views/Resources/Js/sortable.min.js')
            ->addScriptAfter('App/Package/Core/Views/Resources/Js/menu.js');
        $view->view();
    }

    public function getPackagesLinks(): array
    {
        $toReturn = [];
        $data = Loader::loadImplementations(IMenus::class);

        foreach ($data as $package):
            $toReturn[$package->getPackageName()] = $package->getRoutes();
        endforeach;

        return $toReturn;
    }

    #[NoReturn] #[Link('/', Link::POST, [], '/cmw-admin/menus')]
    private function adminMenusAddPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.menu');

        [$name, $choice] = Utils::filterInput('name', 'choice');

        $targetBlank = empty($_POST['targetBlank']) ? 0 : 1;
        $isRestricted = empty($_POST['allowedGroupsToggle']) ? 0 : 1;

        if ($choice === 'package') {
            $url = Utils::filterInput('slugPackage')[0];
            $customUrl = 0;
        } else {
            $url = Utils::filterInput('slugCustom')[0];
            $customUrl = 1;
        }

        $menu = MenusModel::getInstance()->createMenu($name, $url, $targetBlank, $isRestricted, $customUrl);

        if (is_null($menu)) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'));

            Redirect::redirectPreviousRoute();
        }

        if (!empty($_POST['allowedGroupsToggle']) && !empty($_POST['allowedGroups'])) {
            foreach ($_POST['allowedGroups'] as $roleId) {
                MenusModel::getInstance()->addMenuGroupsAllowed($menu->getId(), $roleId);
            }
        }

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('core.menus.add.toaster.success'));

        Redirect::redirectPreviousRoute();
    }

    #[Link('/add-submenu/:menuId', Link::GET, [], '/cmw-admin/menus')]
    private function adminMenusAddSub(int $menuId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.menu');

        $packagesLinks = $this->getPackagesLinks();
        $roles = RolesModel::getInstance()->getRoles();
        $instanceMenu = MenusModel::getInstance()->getMenuById($menuId);

        $view = View::createAdminView('Core', 'Menu/addSub')
            ->addVariableList(['packagesLinks' => $packagesLinks, 'roles' => $roles, 'instanceMenu' => $instanceMenu])
            ->addScriptAfter('App/Package/Core/Views/Resources/Js/menu.js');;
        $view->view();
    }

    #[NoReturn]
    #[Link('/add-submenu/:menuId', Link::POST, [], '/cmw-admin/menus')]
    private function adminMenusAddSubPost(int $menuId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.menu');

        [$name, $choice, $targetBlank] = Utils::filterInput('name', 'choice', 'targetBlank');

        if ($targetBlank === null) {
            $targetBlank = 0;
        } else {
            $targetBlank = 1;
        }
        $isRestricted = empty($_POST['allowedGroupsToggle']) ? 0 : 1;

        if ($choice === 'package') {
            $url = Utils::filterInput('slugPackage')[0];
            $customUrl = 0;
        } else {
            $url = Utils::filterInput('slugCustom')[0];
            $customUrl = 1;
        }

        $menu = MenusModel::getInstance()->createSubMenu($name, $menuId, $url, $targetBlank, $isRestricted, $customUrl);

        if (is_null($menu)) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'));

            // TODO redirect vers /menus
            Redirect::redirectPreviousRoute();
        }

        if (!empty($_POST['allowedGroupsToggle']) && !empty($_POST['allowedGroups'])) {
            foreach ($_POST['allowedGroups'] as $roleId) {
                MenusModel::getInstance()->addMenuGroupsAllowed($menu->getId(), $roleId);
            }
        }

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('core.menus.add.toaster.success'));

        Redirect::redirectPreviousRoute();
    }

    #[Link('/edit/:menuId', Link::GET, [], '/cmw-admin/menus')]
    private function adminMenusEdit(int $menuId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.menu');

        $packagesLinks = $this->getPackagesLinks();
        $roles = RolesModel::getInstance()->getRoles();
        $instanceMenu = MenusModel::getInstance()->getMenuById($menuId);

        $view = View::createAdminView('Core', 'Menu/edit')
            ->addVariableList(['packagesLinks' => $packagesLinks, 'roles' => $roles, 'instanceMenu' => $instanceMenu])
            ->addScriptAfter('App/Package/Core/Views/Resources/Js/menu.js');;
        $view->view();
    }

    #[NoReturn]
    #[Link('/edit/:menuId', Link::POST, [], '/cmw-admin/menus')]
    private function adminMenusEditPost(int $menuId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.menu');

        [$name, $choice, $targetBlank] = Utils::filterInput('name', 'choice', 'targetBlank');

        if ($targetBlank === null) {
            $targetBlank = 0;
        } else {
            $targetBlank = 1;
        }
        $isRestricted = empty($_POST['allowedGroupsToggle']) ? 0 : 1;

        if ($choice === 'package') {
            $url = Utils::filterInput('slugPackage')[0];
            $customUrl = 0;
        } else {
            $url = Utils::filterInput('slugCustom')[0];
            $customUrl = 1;
        }

        $menu = MenusModel::getInstance()->editMenu($menuId, $name, $url, $targetBlank, $isRestricted, $customUrl);

        if (is_null($menu)) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'));

            // TODO redirect vers /menus
            Redirect::redirectPreviousRoute();
        }

        if ($isRestricted === 0) {
            MenusModel::getInstance()->deleteMenuGroupsAllowed($menu->getId());
        }

        if (!empty($_POST['allowedGroupsToggle']) && !empty($_POST['allowedGroups'])) {
            MenusModel::getInstance()->deleteMenuGroupsAllowed($menu->getId());
            foreach ($_POST['allowedGroups'] as $roleId) {
                MenusModel::getInstance()->addMenuGroupsAllowed($menu->getId(), $roleId);
            }
        }

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            'Menu éditer !');

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/delete/:id/:currentOrder', Link::GET, ['id' => '[0-9]+'], '/cmw-admin/menus')]
    private function adminMenuDelete(int $id, int $currentOrder): void
    {
        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'), 'Menu supprimé');

        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.menu');

        menusModel::getInstance()->deleteMenu($id, $currentOrder);

        /*Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate("contact.toaster.delete.success"));*/

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/delete/:id/:currentOrder/:parentId', Link::GET, ['id' => '[0-9]+'], '/cmw-admin/menus')]
    private function adminSubMenuDelete(int $id, int $currentOrder, int $parentId): void
    {
        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'), 'Menu supprimé');

        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.menu');

        menusModel::getInstance()->deleteSubMenu($id, $currentOrder, $parentId);

        /*Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate("contact.toaster.delete.success"));*/

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/menuUp/:id/:currentOrder', Link::GET, ['id' => '[0-9]+'], '/cmw-admin/menus')]
    private function adminMenuUp(int $id, int $currentOrder): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.menu');

        menusModel::getInstance()->upPositionMenu($id, $currentOrder);

        Redirect::redirect('cmw-admin/menus');
    }

    #[NoReturn] #[Link('/menuDown/:id/:currentOrder', Link::GET, ['id' => '[0-9]+'], '/cmw-admin/menus')]
    private function adminMenuDown(int $id, int $currentOrder): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.menu');

        menusModel::getInstance()->downPositionMenu($id, $currentOrder);

        Redirect::redirect('cmw-admin/menus');
    }

    #[NoReturn] #[Link('/submenuUp/:id/:currentOrder/:parentId', Link::GET, ['id' => '[0-9]+'], '/cmw-admin/menus')]
    private function adminSubMenuUp(int $id, int $currentOrder, int $parentId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.menu');

        menusModel::getInstance()->upPositionSubMenu($id, $currentOrder, $parentId);

        Redirect::redirect('cmw-admin/menus');
    }

    #[NoReturn] #[Link('/submenuDown/:id/:currentOrder/:parentId', Link::GET, ['id' => '[0-9]+'], '/cmw-admin/menus')]
    private function adminSubMenuDown(int $id, int $currentOrder, int $parentId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.menu');

        menusModel::getInstance()->downPositionSubMenu($id, $currentOrder, $parentId);

        Redirect::redirect('cmw-admin/menus');
    }

    /**
     * @param \CMW\Manager\Package\PackageSubMenuType[] $subMenus
     * @return bool
     */
    public function isActiveNavbar(array $subMenus): bool
    {
        $currentSlug = str_replace(EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'cmw-admin/', '', $_SERVER['REQUEST_URI']);

        foreach ($subMenus as $subMenu) {
            if (str_starts_with($currentSlug, $subMenu->getUrl())) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $url
     * @return bool
     */
    public function isActiveNavbarItem(string $url): bool
    {
        $currentSlug = str_replace(EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'cmw-admin/', '', $_SERVER['REQUEST_URI']);

        return str_starts_with($currentSlug, $url);
    }
}

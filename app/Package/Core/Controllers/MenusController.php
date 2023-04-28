<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Interface\Core\IMenus;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Views\View;
use CMW\Model\Core\MenusModel;
use CMW\Model\Users\RolesModel;
use CMW\Router\Link;
use CMW\Utils\Loader;
use CMW\Utils\Redirect;
use CMW\Utils\Response;
use CMW\Utils\Utils;

/**
 * Class: @MenusController
 * @package Core
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class MenusController extends CoreController
{

    private MenusModel $menusModel;

    public function __construct($theme_path = null)
    {
        parent::__construct($theme_path);
        $this->menusModel = new MenusModel();
    }

    public function cmwMenu(): array
    {
        return []; //TODO
    }

    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin/menus")]
    #[Link("/", Link::GET, [], "/cmw-admin/menus")]
    public function adminMenus(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.menus.configuration");

        $view = View::createAdminView('core', 'menus')
            ->addVariableList(['packagesLinks' => $this->getPackagesLinks(), 'roles' => (new RolesModel())->getRoles(),
                'menus' => $this->menusModel->getMenus()])
            ->addScriptBefore("app/package/core/views/resources/js/sortable.min.js")
            ->addScriptAfter("app/package/core/views/resources/js/menu.js");
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

    #[Link("/", Link::POST, [], "/cmw-admin/menus")]
    public function adminMenusAddPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.menus.configuration");

        [$name, $choice] = Utils::filterInput('name', 'choice');

        $targetBlank = empty($_POST['targetBlank']) ? 0 : 1;
        $isRestricted = empty($_POST['allowedGroupsToggle']) ? 0 : 1;

        if ($choice === 'package') {
            $url = Utils::filterInput('slugPackage')[0];
        } else {
            $url = Utils::filterInput('slugCustom')[0];
        }

        $menu = $this->menusModel->createMenu($name, $url, $targetBlank, $isRestricted);

        if (is_null($menu)) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));

            Redirect::redirectPreviousRoute();
            return;
        }


        if (!empty($_POST['allowedGroupsToggle']) && !empty($_POST['allowedGroups'])) {
            foreach ($_POST['allowedGroups'] as $roleId) {
                $this->menusModel->addMenuGroupsAllowed($menu->getId(), $roleId);
            }
        }

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("core.menus.add.toaster.success"));

        Redirect::redirectPreviousRoute();
    }
}
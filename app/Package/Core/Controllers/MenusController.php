<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Model\Core\MenusModel;
use CMW\Router\Link;
use CMW\Manager\Views\View;

/**
 * Class: @MenusController
 * @package Core
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class MenusController extends CoreController
{

    private MenusModel $menusModel;


    /* //////////////////////////////////////////////////////////////////////////// */
    /* GLOBALS FUNCTIONS */
    /*
     * Retrieving the menu saved in the database
     */


    public function __construct($theme_path = null)
    {
        parent::__construct($theme_path);
        $this->menusModel = new MenusModel();
    }

    public function cmwMenu(): array
    {
        return (new MenusModel())->fetchMenu();
    }

    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin/menus")]
    #[Link("/", Link::GET, [], "/cmw-admin/menus")]
    public function adminMenus(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.menus.configuration");

        $view = View::createAdminView('core', 'menus')
            ->addScriptBefore("app/package/core/views/resources/js/sortable.min.js")
            ->addScriptAfter("app/package/core/views/resources/js/menu.js");
        $view->view();
    }
}
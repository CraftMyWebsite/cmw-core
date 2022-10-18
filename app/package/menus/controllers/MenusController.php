<?php

namespace CMW\Controller\Menus;

use CMW\Controller\Core\CoreController;
use CMW\Model\Menus\MenusModel;
use CMW\Router\Link;
use CMW\Utils\View;

/**
 * Class: @menusController
 * @package Menus
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
        $coreModel = new MenusModel();

        return $coreModel->fetchMenu();
    }

    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin/menus")]
    #[Link("/", Link::GET, [], "/cmw-admin/menus")]
    public function adminMenus(): void
    {
        $view = View::createAdminView('menus', 'menus')
            ->addScriptBefore("app/package/menus/views/assets/js/sortable.min.js")
            ->addScriptAfter("app/package/menus/views/assets/js/main.js");

        $view->view();
    }
}
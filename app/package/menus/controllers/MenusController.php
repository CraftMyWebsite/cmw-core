<?php

namespace CMW\Controller\Menus;

use CMW\Controller\CoreController;
use CMW\Model\Menus\MenusModel;

/**
 * Class: @menusController
 * @package Menus
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class MenusController extends CoreController {

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

    public function adminMenus() : void {
        view('menus', 'menus.admin', [], 'admin');
    }
}
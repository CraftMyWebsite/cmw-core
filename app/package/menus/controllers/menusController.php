<?php

namespace CMW\Controller\Menus;

use CMW\Controller\coreController;
use CMW\Model\Menus\menusModel;

/**
 * Class: @menusController
 * @package Menus
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class menusController extends coreController {

    private menusModel $menusModel;


    /* //////////////////////////////////////////////////////////////////////////// */
    /* GLOBALS FUNCTIONS */
    /*
     * Retrieving the menu saved in the database
     */


    public function __construct($theme_path = null)
    {
        parent::__construct($theme_path);
        $this->menusModel = new menusModel();
    }

    public function cmwMenu(): array
    {
        $coreModel = new menusModel();

        return $coreModel->fetchMenu();
    }

    public function adminMenus() : void {
        view('menus', 'menus.admin', [], 'admin');
    }
}
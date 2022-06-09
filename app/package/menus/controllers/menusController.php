<?php

namespace CMW\Controller\Menus;

use CMW\Controller\coreController;
use CMW\Model\Menus\menusModel;

/**
 * Class: @menusController
 * @package Menus
 * @author LoGuardiaN
 * @version 1.0
 */
class menusController extends coreController {
    /* //////////////////////////////////////////////////////////////////////////// */
    /* GLOBALS FUNCTIONS */
    /*
     * Retrieving the menu saved in the database
     */
    public function cmwMenu(): array
    {
        $coreModel = new menusModel();
        $coreModel->fetchMenu();

        return $coreModel->menu;
    }

    public function adminMenus() : void {
        view('menus', 'menus.admin', [], 'admin');
    }
}
<?php

namespace CMW\Model\Menus;

use CMW\Model\Manager;

/**
 * Class: @menusModel
 * @package Menus
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class MenusModel extends Manager {

    /* Get the menu
     *
     */
    public function fetchMenu(): array
    {
        $db = self::dbConnect();
        $req = $db->query('SELECT menu_id, menu_name, menu_url, menu_level, menu_parent_id FROM cmw_menus');
        return $req->fetchAll(\PDO::FETCH_CLASS);
    }
}

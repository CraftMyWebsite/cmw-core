<?php

namespace CMW\Model\Core;

use CMW\Manager\Database\DatabaseManager;

/**
 * Class: @MenusModel
 * @package Core
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class MenusModel extends DatabaseManager {

    /* Get the menu
     *
     */
    public function fetchMenu(): array
    {
        $db = self::getInstance();
        $req = $db->query('SELECT menu_id, menu_name, menu_url, menu_level, menu_parent_id FROM cmw_menus');
        return $req->fetchAll(\PDO::FETCH_CLASS);
    }
}

<?php

namespace CMW\Model\Core;

use CMW\Entity\Core\MenuEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;

/**
 * Class: @MenusModel
 * @package Core
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class MenusModel extends AbstractModel
{

    /**
     * @param string $name
     * @param string $url
     * @param int $targetBlank
     * @param int $isRestricted
     * @return \CMW\Entity\Core\MenuEntity|null
     */
    public function createMenu(string $name, string $url, int $targetBlank, int $isRestricted): ?MenuEntity
    {
        $var = [
            "name" => $name,
            "url" => $url,
            "menu_order" => $this->getLastMenuOrder() + 1,
            "target_blank" => $targetBlank,
            "restricted" => $isRestricted
        ];

        $sql = "INSERT INTO cmw_menus (menu_name, menu_url, menu_is_restricted, menu_order, menu_target_blank) 
                VALUES (:name, :url, :restricted,:menu_order ,:target_blank)";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute($var)){
            return null;
        }

        $id = $db->lastInsertId();

        return $this->getMenuById($id);
    }

    /**
     * @param int $id
     * @return \CMW\Entity\Core\MenuEntity|null
     */
    public function getMenuById(int $id): ?MenuEntity
    {
        $sql = "SELECT * FROM cmw_menus WHERE menu_id = :id";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(['id' => $id])){
            return null;
        }

        $res = $req->fetch();

        if (!$res){
            return null;
        }

        return new MenuEntity(
            $res['menu_id'],
            $res['menu_name'],
            $res['menu_url'],
            $res['menu_is_restricted'],
            $res['menu_order'],
            $res['menu_target_blank']
        );
    }

    /**
     * @return MenuEntity[]
     */
    public function getMenus(): array
    {
        $sql = "SELECT menu_id FROM cmw_menus ORDER BY menu_order";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req){
            return [];
        }

        if (!$req->execute()) {
            return array();
        }

        $toReturn = array();

        while ($menu = $req->fetch()) {
            $toReturn[] = $this->getMenuById($menu["menu_id"]);
        }

        return $toReturn;
    }

    /**
     * @return int
     * @desc We ignore menus with menu_parent_id
     */
    public function getLastMenuOrder(): int
    {
        $sql = "SELECT menu_order FROM cmw_menus WHERE menu_parent_id IS null ORDER BY menu_order DESC LIMIT 1";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute()){
            return 0;
        }

        $res = $req->fetch();

        if(!$res){
            return 0;
        }

        return $res['menu_order'] ?? 0;
    }

    public function addMenuGroupsAllowed(int $menuId, int $roleId): void
    {
        $sql = "INSERT INTO cmw_menus_groups_allowed (menus_groups_group_id, menus_groups_menu_id)
                VALUES (:group_id, :menu_id)";
        $db = DatabaseManager::getInstance();
        $req = $db ->prepare($sql);
        $req->execute(['group_id' => $roleId, 'menu_id' => $menuId]);
    }

}

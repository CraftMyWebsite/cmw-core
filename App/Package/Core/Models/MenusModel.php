<?php

namespace CMW\Model\Core;

use CMW\Entity\Core\MenuEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\RolesModel;

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
     * @param string $name
     * @param string $parentId
     * @param string $url
     * @param int $targetBlank
     * @param int $isRestricted
     * @return \CMW\Entity\Core\MenuEntity|null
     */
    public function createSubMenu(string $name,string $parentId, string $url, int $targetBlank, int $isRestricted): ?MenuEntity
    {
        $var = [
            "name" => $name,
            "parentId" => $parentId,
            "url" => $url,
            "menu_order" => $this->getLastSubMenuOrder($parentId) + 1,
            "target_blank" => $targetBlank,
            "restricted" => $isRestricted
        ];

        $sql = "INSERT INTO cmw_menus (menu_name, menu_parent_id, menu_url, menu_is_restricted, menu_order, menu_target_blank) 
                VALUES (:name, :parentId, :url, :restricted,:menu_order ,:target_blank)";

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
            $res['menu_target_blank'],
            $this->getAllowedRoles($res['menu_id'])
        );
    }

    /**
     * @return MenuEntity[]
     */
    public function getMenus(): array
    {
        $sql = "SELECT menu_id FROM cmw_menus WHERE menu_parent_id IS NULL ORDER BY menu_order";
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
     * @return MenuEntity[]
     */
    public function getSubMenusByMenu(int $id): array
    {
        $sql = "SELECT menu_id FROM cmw_menus WHERE menu_parent_id = :menu_id ORDER BY menu_order";
        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        if (!$res->execute(array("menu_id" => $id))) {
            return array();
        }

        $toReturn = array();

        while ($menu = $res->fetch()) {
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


    /**
     * @return int
     * @desc We ignore menus with menu_parent_id
     */
    public function getLastSubMenuOrder(int $parentId): int
    {
        $sql = "SELECT menu_order FROM cmw_menus WHERE menu_parent_id = :parentID ORDER BY menu_order DESC LIMIT 1";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(array("parentID" => $parentId))) {
            return 0;
        }

        $res = $req->fetch();

        if(!$res){
            return 0;
        }

        return $res['menu_order'] ?? 0;
    }

    /**
     * @return int
     * @desc Up the position of menu
     */
    public function getMenuIdByOrder(int $currentOrder): int
    {
        $sql = "SELECT menu_id FROM cmw_menus WHERE menu_order = :currentOrder";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(array("currentOrder" => $currentOrder))) {
            return 0;
        }
        $res = $req->fetch();
        if(!$res){
            return 0;
        }
        return $res['menu_id'] ?? 0;
    }

    /**
     * @return int
     * @desc Up the position of menu
     */
    public function getSubMenuIdByOrder(int $currentOrder, int $parentId): int
    {
        $sql = "SELECT menu_id FROM cmw_menus WHERE menu_order = :currentOrder AND menu_parent_id = :parentId";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(array("currentOrder" => $currentOrder, "parentId" => $parentId))) {
            return 0;
        }
        $res = $req->fetch();
        if(!$res){
            return 0;
        }
        return $res['menu_id'] ?? 0;
    }

    /**
     * @return int
     * @desc Up the position of the next menu
     */
    public function updateNextMenuIdByOrder(int $currentOrder): void
    {
        $id = $this->getMenuIdByOrder($currentOrder + 1);
        $sql = "UPDATE cmw_menus SET menu_order = :newOrder WHERE menu_id = :id";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute(array("id" => $id, "newOrder" => $currentOrder));
    }

    /**
     * @return int
     * @desc Up the position of the next submenu
     */
    public function updateNextSubMenuIdByOrder(int $currentOrder, int $parentId): void
    {
        $id = $this->getSubMenuIdByOrder($currentOrder + 1, $parentId);
        $sql = "UPDATE cmw_menus SET menu_order = :newOrder WHERE menu_id = :id";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute(array("id" => $id, "newOrder" => $currentOrder));
    }

    /**
     * @return int
     * @desc Down the position of the previous menu
     */
    public function updatePreviousMenuIdByOrder(int $currentOrder): void
    {
        $id = $this->getMenuIdByOrder($currentOrder - 1);
        $sql = "UPDATE cmw_menus SET menu_order = :newOrder WHERE menu_id = :id";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute(array("id" => $id, "newOrder" => $currentOrder));
    }

    /**
     * @return int
     * @desc Down the position of the previous menu
     */
    public function updatePreviousSubMenuIdByOrder(int $currentOrder, int $parentId): void
    {
        $id = $this->getSubMenuIdByOrder($currentOrder - 1, $parentId);
        $sql = "UPDATE cmw_menus SET menu_order = :newOrder WHERE menu_id = :id";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute(array("id" => $id, "newOrder" => $currentOrder));
    }

    /**
     * @return null
     * @desc Up the position of menu
     */
    public function upPositionMenu(int $id, int $currentOrder): void
    {
        $this->updateNextMenuIdByOrder($currentOrder);
        $sql = "UPDATE cmw_menus SET menu_order = :newOrder WHERE menu_id = :id";
        $newOrder = $currentOrder + 1;
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute(array("id" => $id, "newOrder" => $newOrder));
    }

    /**
     * @return null
     * @desc Up the position of submenu
     */
    public function upPositionSubMenu(int $id, int $currentOrder, int $parentId): void
    {
        $this->updateNextSubMenuIdByOrder($currentOrder, $parentId);
        $sql = "UPDATE cmw_menus SET menu_order = :newOrder WHERE menu_id = :id";
        $newOrder = $currentOrder + 1;
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute(array("id" => $id, "newOrder" => $newOrder));
    }

    /**
     * @return null
     * @desc Down the position of menu
     */
    public function downPositionMenu(int $id, int $currentOrder): void
    {
        $this->updatePreviousMenuIdByOrder($currentOrder);
        $sql = "UPDATE cmw_menus SET menu_order = :newOrder WHERE menu_id = :id";
        $newOrder = $currentOrder - 1;
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute(array("id" => $id, "newOrder" => $newOrder));
    }

    /**
     * @return null
     * @desc Down the position of submenu
     */
    public function downPositionSubMenu(int $id, int $currentOrder, int $parentId): void
    {
        $this->updatePreviousSubMenuIdByOrder($currentOrder, $parentId);
        $sql = "UPDATE cmw_menus SET menu_order = :newOrder WHERE menu_id = :id";
        $newOrder = $currentOrder - 1;
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute(array("id" => $id, "newOrder" => $newOrder));
    }

    public function addMenuGroupsAllowed(int $menuId, int $roleId): void
    {
        $sql = "INSERT INTO cmw_menus_groups_allowed (menus_groups_group_id, menus_groups_menu_id)
                VALUES (:group_id, :menu_id)";
        $db = DatabaseManager::getInstance();
        $req = $db ->prepare($sql);
        $req->execute(['group_id' => $roleId, 'menu_id' => $menuId]);
    }


    /**
     * @param int $menuId
     * @return \CMW\Entity\Users\RoleEntity[]|null
     */
    public function getAllowedRoles(int $menuId): ?array
    {
        $sql = "SELECT menus_groups_group_id FROM cmw_menus_groups_allowed WHERE menus_groups_menu_id = :id";
        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(['id' => $menuId])){
            return null;
        }


        $roles = [];
        while ($role = $req->fetch()) {
            $roles[] = RolesModel::getInstance()->getRoleById($role['menus_groups_group_id']);
        }

        return $roles;
    }

    /**
     * @return null
     * @param int $currentOrder
     * @desc Re order the menu need to know the current order
     */
    public function reorderMenu(int $currentOrder): void
    {
        $sql = "UPDATE cmw_menus SET menu_order = menu_order - 1 WHERE menu_order > :currentOrder";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute(array("currentOrder" => $currentOrder));
    }

    /**
     * @return null
     * @param int $currentOrder
     * @desc Re order the submenu need to know the current order and parentId
     */
    public function reorderSubMenu(int $currentOrder, int $parentId): void
    {
        $sql = "UPDATE cmw_menus SET menu_order = menu_order - 1 WHERE menu_order > :currentOrder AND menu_parent_id = :parentId";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute(array("currentOrder" => $currentOrder, "parentId" => $parentId));
    }

    /**
     * @return null
     * @param int $id
     * @param int $currentOrder
     * @desc Delete the menu and caal reorder function
     */
    public function deleteMenu(int $id, int $currentOrder): void
    {
        $sql = "DELETE FROM cmw_menus WHERE menu_id=:id";
        $this->reorderMenu($currentOrder);
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute(array("id" => $id));
    }

    /**
     * @return null
     * @param int $id
     * @param int $currentOrder
     * @desc Delete the menu and caal reorder function
     */
    public function deleteSubMenu(int $id, int $currentOrder, int $parentId): void
    {
        $sql = "DELETE FROM cmw_menus WHERE menu_id=:id";
        $this->reorderSubMenu($currentOrder, $parentId);
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute(array("id" => $id));
    }

}

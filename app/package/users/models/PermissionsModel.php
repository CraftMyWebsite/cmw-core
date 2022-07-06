<?php

namespace CMW\Model\Permissions;

use CMW\Entity\Roles\RoleEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Entity\Permissions\PermissionEntity;
use CMW\Model\Manager;

/**
 * Class: @permissionsModel
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class PermissionsModel extends Manager
{

    public function getPermissionById(int $id): ?PermissionEntity
    {

        $sql = "SELECT * FROM cmw_permissions2 WHERE permission_id = :permission_id";

        $db = Manager::dbConnect();
        $req = $db->prepare($sql);

        if (!$req->execute(array("permission_id" => $id))) {
            return null;
        }

        $res = $req->fetch();

        if (!$res) {
            return null;
        }

        $parentEntity = null;

        if (!is_null($res["permission_parent_id"])) {
            $parentEntity = $this->getPermissionById($res["permission_parent_id"]);
        }

        return new PermissionEntity(
            $id,
            $parentEntity,
            $res["permission_code"],
            $res['permission_editable']
        );

    }

    public function getFullPermissionCodeById(int $id, string $separationChar = "."): string
    {

        $permissionEntity = $this->getPermissionById($id);

        if (is_null($permissionEntity)) {
            return "";
        }

        $toReturn = array($permissionEntity->getPermissionCode());

        while (!is_null($permissionEntity->getPermissionParent())) {

            $permissionEntity = $permissionEntity->getPermissionParent();
            $toReturn[] = $permissionEntity->getPermissionCode();

        }

        return implode($separationChar, array_reverse($toReturn));

    }

}
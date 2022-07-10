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

    /**
     * @return array
     */
    public function getPermissions(): array
    {
        $toReturn = array();

        /* Get all parents + desc */
        $sql = "SELECT permParent.*, permDesc.permission_desc_code_parent, permDesc.permission_desc_value, permission_desc_lang 
                FROM cmw_permissions_parent AS permParent 
                JOIN cmw_permissions_desc permDesc
                ON permParent.permission_parent_code = permDesc.permission_desc_code_parent
                WHERE permDesc.permission_desc_lang = :lang";
        $db = Manager::dbConnect();
<<<<<<< Updated upstream
=======
        $req = $db->prepare($sql);

        if (!$req->execute(array("permission_code" => $code))) {
            return array();
        }

        $toReturn = array();

        while ($res = $req->fetch()) {

            $permissionEntity = $this->getPermissionById($res["permission_id"]);

            Utils::addIfNotNull($toReturn, $permissionEntity);

        }


        return $toReturn;
    }

    /** With an parsed code (like <b>users.edit</b>), get permission Entity
     * @param string $code Parsed code
     * @return \CMW\Entity\Permissions\PermissionEntity|null
     */
    public function getPermissionByFullCode(string $code): ?PermissionEntity
    {
        $codeList = explode(".", $code);
>>>>>>> Stashed changes

        $resParent = $db->prepare($sql);

        if($resParent->execute(array("lang" => getenv("LOCALE")))){

            $resParent = $resParent->fetchAll();


            foreach ($resParent as $parent){


                $sql = "SELECT permChild.*, permDesc.permission_desc_code_parent, permDesc.permission_desc_value, permission_desc_lang
                FROM cmw_permissions_child AS permChild
                JOIN cmw_permissions_desc permDesc
                ON permChild.permission_child_code = permDesc.permission_desc_code_child
                WHERE permChild.permission_child_parent = :parent";

                $resChild = $db->prepare($sql);



<<<<<<< Updated upstream
                if(!$resChild->execute(array("parent" => $parent['permission_parent_code']))){
                    continue;
                }
=======
            $idCodeList[] = $elm->getId();
        }

        return $this->getPermissionById($idCodeList[count($idCodeList) - 1]);

    }

    /**
     * @return \CMW\Entity\Permissions\PermissionEntity[]
     */
    public function getPermissions(): array
    {

        $sql = "SELECT permission_id FROM cmw_permissions ORDER BY permission_parent_id";
        $db = self::dbConnect();
        $req = $db->query($sql);

        if (!$req) {
            return array();
        }

        $toReturn = array();

        while ($perm = $req->fetch()) {
            Utils::addIfNotNull($toReturn, $this->getPermissionById($perm["permission_id"]));
        }

        return $toReturn;
    }

    /**
     * @return \CMW\Entity\Permissions\PermissionEntity[]
     */
    public function getParents(): array
    {
        $sql = "SELECT permission_id FROM cmw_permissions WHERE permission_parent_id IS NULL";
        $db = self::dbConnect();
        $req = $db->query($sql);

        if (!$req) {
            return array();
        }

        $toReturn = array();

        while ($perm = $req->fetch()) {
            Utils::addIfNotNull($toReturn, $this->getPermissionById($perm["permission_id"]));
        }

        return $toReturn;
    }


    /**==> ADDS */

    public function addParentPermission(string $code): ?PermissionEntity
    {

        $parentList = $this->getPermissionsByLastCode($code);

        foreach ($parentList as $parent) {
            if (!is_null($parent) && !($parent->hasParent())) {
                return $parent;
            }
        }

        $sql = "INSERT INTO cmw_permissions(permission_parent_id, permission_code) VALUES (null, :permission_code)";

        $db = self::dbConnect();

        $req = $db->prepare($sql);

        if ($req->execute(array("permission_code" => $code))) {
            $id = $db->lastInsertId();
            return new PermissionEntity($id, null, $code);
        }

        return null;

    }

    public function addChildPermission(int $parentId, string $code): ?PermissionEntity
    {
        $parent = $this->getPermissionById($parentId);


        if (is_null($parent)) {
            return null;
        }

        $permissionChild = $this->getPermissionByParentId($parent->getId());
        foreach ($permissionChild as $child) {
            if (!is_null($child) && $child->getCode() === $code) {
                return $child;
            }
        }

        $sql = "INSERT INTO cmw_permissions(permission_parent_id, permission_code) VALUES (:parent_id, :permission_code)";

        $db = self::dbConnect();

        $req = $db->prepare($sql);

        if ($req->execute(array("parent_id" => $parentId, "permission_code" => $code))) {
            $id = $db->lastInsertId();
            return new PermissionEntity($id, $parent, $code);
        }

        return null;
    }

    public function addFullCodePermission(string $code): ?PermissionEntity
    {

        if (!is_null($this->getPermissionByFullCode($code))) {
            return null;
        }

        $values = explode(".", $code);
        $actualPermission = null;

        foreach ($values as $key => $value) {
            $actualPermission = ($key === 0)
                ? $this->addParentPermission($value)
                : $this->addChildPermission($actualPermission->getId(), $value);
        }

        return $actualPermission;
    }

    /**==> UTILS */

    /**
     * @param PermissionEntity[] $permissionList
     * @param string $code Permission Code to test, need to be a child permission (<b>users.edit</b>)<br>
     * Don't use <b>users</b> or <b>users.*</b> !
     */
    public static function hasPermissions(array $permissionList, string $code): bool
    {
>>>>>>> Stashed changes

                $resChild = $resChild->fetchAll();


                if(!$resChild){
                    continue;
                }


                $toReturn += array($parent['permission_parent_package'] => [
                    "package" => $parent['permission_parent_package'],
                    "parent_code" => $parent['permission_parent_code'],
                    "parent_editable" => $parent['permission_parent_editable'],
                    "parent_desc_value" => $parent['permission_desc_value'],
                    "perms_childs" => []]);

                foreach ($resChild as $child){
                    $toReturn[$parent['permission_parent_package']]['perms_childs'] += [
                            $child['permission_child_code'] => [
                            "child_code" => $child['permission_child_code'],
                            "child_editable" => $child['permission_child_editable'],
                            "child_desc_value" => $child['permission_desc_value']
                        ]
                  ];
                }

<<<<<<< Updated upstream
=======
    private function checkPermission(PermissionEntity $permissionEntity, string $code): bool
    {
        $operatorPermission = "operator";
>>>>>>> Stashed changes


            }
        }


<<<<<<< Updated upstream
        return $toReturn;
=======
        return false;

    }

    public function hasChild($permissionId): bool
    {

        $sql = "SELECT COUNT(*) as result FROM cmw_permissions WHERE permission_parent_id = :permission_id";
        $db = Manager::dbConnect();
        $res = $db->prepare($sql);

        if(!$res->execute(array("permission_id" => $permissionId))) {
            return false;
        }

        return $res->fetch()["result"];

>>>>>>> Stashed changes
    }
}
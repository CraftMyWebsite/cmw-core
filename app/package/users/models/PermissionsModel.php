<?php

namespace CMW\Model\Permissions;

use CMW\Entity\Roles\RoleEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Entity\Permissions\PermissionEntity;
use CMW\Model\Manager;
use CMW\Utils\Utils;

/**
 * Class: @permissionsModel
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class PermissionsModel extends Manager
{
    private Utils $utils;

    public function __construct()
    {
        global $_UTILS;
        $this->utils = $_UTILS;
    }

    /**==> GETTERS */

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

    /**
     * Get all permission reattached by his parentId (Can be used to <b>code.* </b>)
     * @param int $parentId
     * @return PermissionEntity[]
     */
    public function getPermissionByParentId(int $parentId): array
    {
        $sql = "SELECT permission_id FROM cmw_permissions2 WHERE permission_parent_id = :permission_parent_id";

        $db = Manager::dbConnect();
        $req = $db->prepare($sql);

        if (!$req->execute(array("permission_parent_id" => $parentId))) {
            return array();
        }

        $toReturn = array();

        while ($res = $req->fetch()) {

            $entity = $this->getPermissionById($res["permission_id"]);

            $this->utils::addIfNotNull($toReturn, $entity);

        }

        return $toReturn;


    }

    /**
     * Parse a child and parent permission to string permission <br>
     * Child: edit, Parent: users will result => users.edit
     * @param int $id last child Id
     * @param string $separationChar Default point, only for decoration users<separationChar>edit.
     * @return string Parsed permission
     */
    public function getFullPermissionCodeById(int $id, string $separationChar = "."): string
    {

        $permissionEntity = $this->getPermissionById($id);

        if (is_null($permissionEntity)) {
            return "";
        }

        $toReturn = array($permissionEntity->getCode());

        while (!is_null($permissionEntity->getCode())) {

            $permissionEntity = $permissionEntity->getPermissionParent();
            $toReturn[] = $permissionEntity->getPermissionCode();

        }

        return implode($separationChar, array_reverse($toReturn));

    }

    /**
     * Get all possible permission entities by last code. <br>
     * edit, can result by an array with user and core edit permissions
     * @param int $limit If -1, send all permission with this code.
     * @return PermissionEntity[]
     */
    public function getPermissionsByLastCode(string $code, int $limit = -1): array
    {

        $sql = "SELECT permission_id FROM cmw_permissions2 WHERE permission_code = :permission_code ORDER BY permission_parent_id ";
        $sql .= $limit > 0 ? "LIMIT $limit" : "";

        $db = Manager::dbConnect();
        $req = $db->prepare($sql);

        if (!$req->execute(array("permission_code" => $code))) {
            return array();
        }

        $toReturn = array();

        while ($res = $req->fetch()) {

            $permissionEntity = $this->getPermissionById($res["permission_id"]);

            $this->utils::addIfNotNull($toReturn, $permissionEntity);

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

        $idCodeList = array();

        foreach ($codeList as $key => $value) {

            $elm = $this->getPermissionsByLastCode($value, 1);

            if (empty($elm)) {
                return null;
            }

            $elm = $elm[0];


            if ($key === 0 && $elm?->hasParent()) {
                return null;
            }

            $parentElement = $elm?->getParent();


            if ($key !== 0 && (is_null($parentElement) || $parentElement->getId() !== $idCodeList[count($idCodeList) - 1])) {
                return null;
            }

            $idCodeList[] = $elm->getId();
        }

        return $this->getPermissionById($idCodeList[count($idCodeList) - 1]);

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

        $sql = "INSERT INTO cmw_permissions2(permission_parent_id, permission_code, permission_editable) VALUES (null, :permission_code, 0)";

        $db = self::dbConnect();

        $req = $db->prepare($sql);

        if ($req->execute(array("permission_code" => $code))) {
            $id = $db->lastInsertId();
            return new PermissionEntity($id, null, $code, 0);
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

        $sql = "INSERT INTO cmw_permissions2(permission_parent_id, permission_code, permission_editable) VALUES (:parent_id, :permission_code, 0)";

        $db = self::dbConnect();

        $req = $db->prepare($sql);

        if ($req->execute(array("parent_id" => $parentId, "permission_code" => $code))) {
            $id = $db->lastInsertId();
            return new PermissionEntity($id, $parent, $code, 0);
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


            if ($key === 0) {
                $actualPermission = $this->addParentPermission($value);
            } else {

                $actualPermission = $this->addChildPermission($actualPermission->getId(), $value);

            }


        }

        return $actualPermission;
    }

}
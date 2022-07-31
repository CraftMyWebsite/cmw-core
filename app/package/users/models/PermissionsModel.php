<?php

namespace CMW\Model\Users;

use CMW\Entity\Users\PermissionEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Utils\Utils;

/**
 * Class: @permissionsModel
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class PermissionsModel extends DatabaseManager
{
    /**==> GETTERS */

    public function getPermissionById(int $id): ?PermissionEntity
    {

        $sql = "SELECT * FROM cmw_permissions WHERE permission_id = :permission_id";

        $db = self::dbConnect();
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
            $res["permission_code"]
        );

    }

    /**
     * Get all permission reattached by his parentId (Can be used to <b>code.* </b>)
     * @param int $parentId
     * @return PermissionEntity[]
     */
    public function getPermissionByParentId(int $parentId): array
    {
        $sql = "SELECT permission_id FROM cmw_permissions WHERE permission_parent_id = :permission_parent_id";

        $db = self::dbConnect();
        $req = $db->prepare($sql);

        if (!$req->execute(array("permission_parent_id" => $parentId))) {
            return array();
        }

        $toReturn = array();

        while ($res = $req->fetch()) {

            $entity = $this->getPermissionById($res["permission_id"]);

            Utils::addIfNotNull($toReturn, $entity);

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

        while (!is_null($permissionEntity->getParent())) {

            $permissionEntity = $permissionEntity->getParent();
            $toReturn[] = $permissionEntity->getCode();

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

        $sql = "SELECT permission_id FROM cmw_permissions WHERE permission_code = :permission_code ORDER BY permission_parent_id ";
        $sql .= $limit > 0 ? "LIMIT $limit" : "";

        $db = self::dbConnect();
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
     * @return \CMW\Entity\Users\PermissionEntity|null
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

        $permissionModel = new self();

        foreach ($permissionList as $permissionEntity) {
            if ($permissionModel->checkPermission($permissionEntity, $code)) {
                return true;
            }

            $permissionChildList = $permissionModel->getPermissionByParentId($permissionEntity->getId());
            foreach ($permissionChildList as $permissionChild) {
                if ($permissionModel->checkPermission($permissionChild, $code)) {
                    return true;
                }
            }

        }

        return false;
    }

    private function checkPermission(PermissionEntity $permissionEntity, string $code): bool
    {
        $operatorPermission = "operator";

        $permissionFullCode = $this->getFullPermissionCodeById($permissionEntity->getId());

        if ($permissionFullCode === $operatorPermission) {
            return true;
        }

        if ($permissionFullCode === $code) {
            return true;
        }

        return false;
    }

    /**
     * @return PermissionEntity[]
     */
    public function getParents(): array
    {

        $sql = "SELECT permission_id FROM cmw_permissions WHERE permission_parent_id IS NULL";

        $db = self::dbConnect();
        $req = $db->prepare($sql);

        if (!$req->execute()) {
            return array();
        }

        $toReturn = array();

        while ($res = $req->fetch()) {

            $entity = $this->getPermissionById($res["permission_id"]);

            Utils::addIfNotNull($toReturn, $entity);

        }

        return $toReturn;

    }

    public function hasChild(int $id): bool
    {

        $sql = "SELECT permission_id FROM cmw_permissions WHERE permission_parent_id = :permission_parent_id";

        $db = self::dbConnect();
        $req = $db->prepare($sql);

        if (!$req->execute(array("permission_parent_id" => $id))) {
            return false;
        }

        return (bool)$req->rowCount();


    }

}
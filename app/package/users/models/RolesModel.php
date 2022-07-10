<?php

namespace CMW\Model\Roles;

use CMW\Entity\Permissions\PermissionEntity;
use CMW\Entity\Roles\RoleEntity;

use CMW\Model\Permissions\PermissionsModel;
use CMW\Model\Users\UsersModel;
use CMW\Model\Manager;

use CMW\Utils\Utils;

/**
 * Class: @rolesModel
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class RolesModel extends Manager
{

    private PermissionsModel $permissionsModel;
    private static UsersModel $usersModel;

    public function __construct()
    {
        $this->permissionsModel = new PermissionsModel();
        self::$usersModel = new UsersModel();
    }

    public function getRoleById($id): ?RoleEntity
    {

        $sql = "SELECT * FROM cmw_roles WHERE role_id = :role_id";

        $db = Manager::dbConnect();
        $req = $db->prepare($sql);

        if (!$req->execute(array("role_id" => $id))) {
            return null;
        }

        $res = $req->fetch();

        if (!$res) {
            return null;
        }

        return new RoleEntity(
            $id,
            $res['role_name'],
            $res['role_description'],
            $res['role_weight'],
            $this->getPermissions($id)
        );

    }

    /**
     * @return RoleEntity[]
     */
    public function getRoles(): array
    {
        $sql = "SELECT role_id FROM cmw_roles";
        $db = Manager::dbConnect();
        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($role = $res->fetch()) {
            Utils::addIfNotNull($toReturn, $this->getRoleById($role["role_id"]));
        }

        return $toReturn;
    }

    public function createRole(string $roleName, string $roleDescription, int $roleWeight, ?array $permList): ?int
    {
        //Create role & return roleId
        $var = array(
            "role_name" => $roleName,
            "role_description" => $roleDescription,
            "role_weight" => $roleWeight
        );

        $sql = "INSERT INTO cmw_roles (role_name, role_description, role_weight) VALUES (:role_name, :role_description, :role_weight)";

        $db = Manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {

            $roleId = $db->lastInsertId();

            //Insert permissions
            foreach ($permList as $permId) {
                $this->addPermission($roleId, $permId);
            }

            return $roleId;
        }


        return null;
    }

    public function addPermission(int $roleId, int $permId): bool
    {
        $sql = "INSERT INTO cmw_roles_permissions VALUES (:permission_id, :role_id)";
        $db = self::dbConnect();
        return $db->prepare($sql)->execute(array("permission_id" => $permId, "role_id" => $roleId));
    }

    /**
     * @return PermissionEntity[]
     */
    public function getPermissions(int $id): array
    {
        $sql = "SELECT permission_id FROM cmw_roles_permissions WHERE role_id = :role_id";
        $db = Manager::dbConnect();
        $res = $db->prepare($sql);

        if (!$res->execute(array("role_id" => $id))) {
            return array();
        }

        $toReturn = array();

        while ($perm = $res->fetch()) {
            Utils::addIfNotNull($toReturn, $this->permissionsModel->getPermissionById($perm["permission_id"]));
        }

        return $toReturn;

    }

    public function roleHasPermission(int $id, string $permCode): int
    {
        $role = $this->getRoleById($id);

        if (is_null($role)) {
            return false;
        }

        $permissionList = $role->getPermissions();
        foreach ($permissionList as $permissionEntity) {
            if ($this->permissionsModel->getFullPermissionCodeById($permissionEntity->getId()) === $permCode) {
                return true;
            }
        }

        return false;
    }

    public function updateRole(string $roleName, string $roleDescription, int $roleId, int $roleWeight, ?array $permList): void
    {
        //Update role
        $var = array(
            "role_name" => $roleName,
            "role_description" => $roleDescription,
            "role_id" => $roleId,
            "role_weight" => $roleWeight
        );

        $sql = "UPDATE cmw_roles SET role_name = :role_name, role_description = :role_description, role_weight = :role_weight WHERE role_id = :role_id";

        $db = Manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);


        $this->updatePermission($roleId, $permList);
    }

    public function updatePermission(int $roleId, ?array $permList): void
    {
        $this->deleteAllPermissions($roleId);
        foreach ($permList as $perm) {
            $this->addPermission($roleId, $perm);
        }
    }

    public function deleteAllPermissions(int $roleId): void
    {
        $sql = "DELETE FROM cmw_roles_permissions WHERE role_id = :role_id";
        $db = self::dbConnect();
        $db->prepare($sql)->execute(array("role_id" => $roleId));
    }

    public function deleteRole(int $roleId): void
    {
        $this->deleteAllPermissions($roleId);

        $var = array(
            "role_id" => $roleId
        );

        $sql = "DELETE FROM cmw_roles WHERE role_id = :role_id";

        $db = Manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);
    }

    public static function playerHasRole(int $userId, int $roleId): bool
    {
        $user = self::$usersModel->getUserById($userId);

        if (is_null($user)) {
            return false;
        }

        $roles = $user->getRoles();
        foreach ($roles as $role) {
            if ($role->getId() === $roleId) {
                return true;
            }
        }

        return false;
    }

}

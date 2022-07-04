<?php

namespace CMW\Model\Roles;

use CMW\Entity\Roles\roleEntity;
use CMW\Model\manager;
use CMW\Model\Permissions\permissionsModel;
use JsonException;

/**
 * Class: @rolesModel
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class rolesModel extends manager
{

    public function fetchAll(): array
    {
        $sql = "SELECT role_id, role_name, role_description, role_weight FROM cmw_roles";
        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute()) {
            return $req->fetchAll();
        }

        return [];
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

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {

            $roleId = $db->lastInsertId();

            //Insert permissions
            if(!empty($permList))
                $this->addPermissions($roleId, $permList);

            return $roleId;
        }


        return null;
    }

    public function addPermissions(int $roleId, ?array $permList): void
    {

        foreach ($permList as $permCode) {

            $var = array(
                "role_permission_code" => $permCode,
                "role_id" => $roleId
            );

            $sql = "INSERT INTO cmw_roles_permissions (role_permission_code, role_permission_role_id) 
                        VALUES(:role_permission_code, :role_id)";

            $db = manager::dbConnect();
            $req = $db->prepare($sql);
            $req->execute($var);

        }

    }

    public function getRoleById($id): ?roleEntity
    {


        $sql = "SELECT * FROM cmw_roles WHERE role_id = :role_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if (!$req->execute(array("role_id" => $id))) {
            return null;
        }

        $res = $req->fetch();

        if (!$res) {
            return null;
        }

        return new roleEntity(
            $id,
            $res['role_name'],
            $res['role_description'],
            $res['role_weight'],
            $this->getRolePermissions($id)
        );

    }

    /**
     * @desc Get all permissions for a role
     */
    public function getRolePermissions($roleId): array
    {

        $toReturn = array();

        $sql = "SELECT * FROM cmw_roles_permissions WHERE role_permission_role_id = :role_id";
        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute(array("role_id" => $roleId))) {
            $res = $req->fetchAll();

            if(!$res)
                return [];

            foreach ($res as $perm){
                $toReturn += array( $perm['role_permission_id'] => [
                    "role_permission_code" => $perm['role_permission_code']
                ]);
            }

        }

        return $toReturn;
    }

    public function roleHasPermission(int $roleId, string $permCode): int
    {
        $var = array(
            "role_id" => $roleId,
            "perm_code" => $permCode
        );

        $sql = "SELECT cmw_roles_permissions.role_permission_code FROM cmw_roles_permissions 
                    WHERE cmw_roles_permissions.role_permission_role_id = :role_id AND cmw_roles_permissions.role_permission_code = :perm_code";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            $lines = $req->fetchAll();

            return count($lines);
        }

        return -1;
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

        $db = manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);


        //Insert permissions
        if (!empty($permList)) {
            $this->updatePermissions($roleId, $permList);
        }
    }

    /***
     * @desc First we delete all the permissions of the role, after we insert the new permissions.
     */
    public function updatePermissions(int $roleId, ?array $permList): void
    {
        //Delete permissions
        $var = array(
            "role_id" => $roleId
        );

        $sql = "DELETE FROM cmw_roles_permissions WHERE role_permission_role_id = :role_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);

        //Add new permissions
        $this->addPermissions($roleId, $permList);

    }

    public function deleteRole(int $roleId): void
    {
        $var = array(
            "role_id" => $roleId
        );

        $sql = "DELETE FROM cmw_roles WHERE role_id = :role_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);
    }

    public static function playerHasRole(int $user_id, int $role_id): bool
    {
        $sql = "SELECT cmw_roles.role_name FROM cmw_users
                    JOIN cmw_users_roles ON cmw_users.user_id = cmw_users_roles.user_id
                    JOIN cmw_roles on cmw_users_roles.role_id = cmw_roles.role_id
                    WHERE cmw_users.user_id = :user_id AND cmw_roles.role_id = :role_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute(array("user_id" => $user_id, "role_id" => $role_id))) {
            return count($req->fetchAll()) > 0;
        }
        return false;
    }

}

<?php

namespace CMW\Model\Roles;

use CMW\Model\manager;

/**
 * Class: @rolesModel
 * @package Users
 * @author Teyir
 * @version 1.0
 */
class rolesModel extends manager {
    //Roles
    public int $roleId;
    public string $roleName;
    public ?string $roleDescription;
    //Perms
    public string $permissionId;
    public string $permissionCode;
    public string $permissionDescription;
    public ?array $permList;


    public function fetchAll(): array
    {
        $sql = "SELECT role_id, role_name, role_description FROM cmw_roles";
        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if($req->execute()) {
            return $req->fetchAll();
        }

        return [];
    }

    /**
     * @throws \JsonException
     * @desc Get all permissions names and code on all installed packages
     */
    public function fetchAllPermissions(): array
    {
        $res = [];
        foreach (getAllPackagesInstalled() as $package):
            $obj = cmwPackageInfo($package);
            if(!empty($obj['permissions'])) {
                $res[] = $obj['permissions'];
            }
        endforeach;

        return $res;
    }

    public function createRole(): void
    {
        //Create role & return roleId
        $var = array(
            "role_name" => $this->roleName,
            "role_description" => $this->roleDescription
        );

        $sql = "INSERT INTO cmw_roles (role_name, role_description) VALUES(:role_name, :role_description)";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            $this->roleId = $db->lastInsertId();
        }

        //Insert permissions
        if (!empty($this->permList)) {
            $this->addPermissions();
        }
    }

    public function addPermissions(): void
    {

        foreach ($this->permList as $permDesc => $permCode):

            $var = array(
                "permission_code" => array_keys($permCode)[0],
                "permission_description" => $permDesc,
                "role_id" => $this->roleId
            );

            $sql = "INSERT INTO cmw_permissions (permission_code, permission_description, role_id) 
                        VALUES(:permission_code, :permission_description, :role_id)";

            $db = manager::dbConnect();
            $req = $db->prepare($sql);
            $req->execute($var);

        endforeach;

    }

    public function fetchRole($id): void
    {
        $var = array(
          "role_id" => $id
        );

        $sql = "SELECT * FROM cmw_roles WHERE role_id = :role_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            $result = $req->fetch();

            foreach ($result as $key => $property) {

                //to camel case all keys (role_id => roleId (for $this->>roleId))
                $key = explode('_', $key);
                $firstElement = array_shift($key);
                $key = array_map('ucfirst', $key);
                array_unshift($key, $firstElement);
                $key = implode('', $key);

                if (property_exists(rolesModel::class, $key)) {
                    $this->$key = $property;
                }
            }
        }
    }

    /**
     * @desc Get all permissions for a role
     */
    public function fetchAllPermissionsForRole($roleId): array
    {
        $var = array(
            "role_id" => $roleId
        );

        $sql = "SELECT * FROM cmw_permissions WHERE role_id = :role_id";
        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            return $req->fetchAll();
        }
        return [];
    }

    public function roleHasPermission(int $roleId, string $permCode): int
    {
        $var = array(
            "role_id" => $roleId,
            "perm_code" => $permCode
        );

        $sql = "SELECT cmw_permissions.permission_code FROM cmw_permissions 
                    WHERE cmw_permissions.role_id = :role_id AND cmw_permissions.permission_code = :perm_code";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if($req->execute($var))
        {
            $lines = $req->fetchAll();

            return count($lines);
        }

        return -1;
    }

    public function updateRole(): void
    {
        //Update role
        $var = array(
            "role_name" => $this->roleName,
            "role_description" => $this->roleDescription,
            "role_id" => $this->roleId
        );

        $sql = "UPDATE cmw_roles SET role_name = :role_name, role_description = :role_description WHERE role_id = :role_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);


        //Insert permissions
        if (!empty($this->permList)) {
            $this->updatePermissions();
        }
    }

    /***
     * @desc First we delete all the permissions of the role, after we insert the new permissions.
     */
    public function updatePermissions(): void
    {
        //Delete permissions
        $var = array(
          "role_id" => $this->roleId
        );

        $sql = "DELETE FROM cmw_permissions WHERE role_id = :role_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);

        //Add new permissions
        $this->addPermissions();

    }

    public function deleteRole(): void
    {
        $var = array(
          "role_id" => $this->roleId
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

        if($req->execute(array("user_id" => $user_id, "role_id" => $role_id))){
            return count($req->fetchAll()) > 0;
        }
        return false;
    }

}

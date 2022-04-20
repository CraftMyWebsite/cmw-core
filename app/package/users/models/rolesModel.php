<?php

namespace CMW\Model\Roles;

use CMW\Model\manager;

/**
 * Class: @rolesModel
 * @package Users
 * @author LoGuardian | <loguardian@hotmail.com>
 * @version 1.0
 */
class rolesModel extends manager {
    public int $roleId;
    public string $roleName;

    public function fetchAll(): array
    {
        $sql = "SELECT role_id, role_name FROM cmw_roles";
        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if($req->execute()) {
            return $req->fetchAll();
        }

        return [];
    }

}

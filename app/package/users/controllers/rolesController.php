<?php

namespace CMW\Controller\Roles;

use CMW\Controller\coreController;
use CMW\Model\Roles\rolesModel;

/**
 * Class: @rolesController
 * @package Users
 * @author LoGuardian | <loguardian@hotmail.com>
 * @version 1.0
 */
class rolesController extends coreController
{
    public function adminRolesList(): void
    {
        $rolesModel = new rolesModel();
        $rolesList = $rolesModel->fetchAll();

        view('users', 'roles.list.admin', ["rolesList" => $rolesList], 'admin');
    }
}
<?php

namespace CMW\Controller\Roles;

use CMW\Controller\coreController;
use CMW\Controller\Users\usersController;
use CMW\Model\Roles\rolesModel;
use CMW\Model\Users\usersModel;
use http\Client\Curl\User;

/**
 * Class: @rolesController
 * @package Users
 * @author LoGuardian et Teyir
 * @version 1.0
 */
class rolesController extends coreController
{
    public function adminRolesList(): void
    {
        usersController::isUserHasPermission("users.roles");

        $rolesModel = new rolesModel();
        $rolesList = $rolesModel->fetchAll();

        view('users', 'roles.list.admin', ["rolesList" => $rolesList], 'admin');
    }

    public function adminRolesAdd(): void
    {
        usersController::isUserHasPermission("users.roles");

        $rolesModel = new rolesModel();
        $permissionsList = $rolesModel->fetchAllPermissions();

        view('users', 'roles.add.admin', ["permissionsList" => $permissionsList], 'admin');
    }

    public function adminRolesAddPost(): void
    {
        usersController::isUserHasPermission("users.roles");

        $role = new rolesModel();
        $role->roleName = filter_input(INPUT_POST, "name");
        $role->roleDescription = filter_input(INPUT_POST, "description");
        $role->permList = $_POST['perms'];
        $role->createRole();


        $_SESSION['toaster'][0]['title'] = USERS_TOASTER_TITLE;
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = USERS_ROLE_ADD_TOASTER_SUCCESS;

        header("location: ../roles/list/");
        die();
    }

    /**
     * @throws \JsonException
     */
    public function adminRolesEdit($id): void
    {
        $role = new rolesModel();
        $role->fetchRole($id);

        $permissionsList = $role->fetchAllPermissions();

        view('users', 'roles.edit.admin', ["role" => $role,
            "permissionsList" => $permissionsList], 'admin');
    }

    public function adminRolesEditPost($id): void
    {
        usersController::isUserHasPermission("users.roles");

        $role = new rolesModel();
        $role->roleName = filter_input(INPUT_POST, "name");
        $role->roleDescription = filter_input(INPUT_POST, "description");
        $role->permList = $_POST['perms'];

        $role->roleId = $id;
        $role->updateRole();

        $_SESSION['toaster'][0]['title'] = USERS_TOASTER_TITLE;
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = USERS_ROLE_EDIT_TOASTER_SUCCESS;

        header("location: ../list/");
        die();
    }

    public function adminRolesDelete($id): void
    {
        usersController::isUserHasPermission("users.roles");

        $role = new rolesModel();
        $role->roleId = $id;
        $role->deleteRole();

        $_SESSION['toaster'][0]['title'] = USERS_TOASTER_TITLE;
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = USERS_ROLE_EDIT_TOASTER_SUCCESS;

        header("location: ../list/");
        die();
    }
}
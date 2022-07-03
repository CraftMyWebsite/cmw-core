<?php

namespace CMW\Controller\Roles;

use CMW\Controller\coreController;
use CMW\Controller\Users\usersController;
use CMW\Model\Permissions\permissionsModel;
use CMW\Model\Roles\rolesModel;
use JetBrains\PhpStorm\NoReturn;
use JsonException;

/**
 * Class: @rolesController
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
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

        $permissionsModel = new permissionsModel();
        $permissionsList = $permissionsModel->getPermissions();

        view('users', 'roles.add.admin', ["permissionsList" => $permissionsList], 'admin');
    }

    #[NoReturn] public function adminRolesAddPost(): void
    {
        usersController::isUserHasPermission("users.roles");

        $role = new rolesModel();
        $roleName = filter_input(INPUT_POST, "name");
        $roleDescription = filter_input(INPUT_POST, "description");
        $permList = $_POST['perms'];
        $roleWeight = filter_input(INPUT_POST, "weight");
        $role->createRole($roleName, $roleDescription, $roleWeight, $permList);


        $_SESSION['toaster'][0]['title'] = USERS_TOASTER_TITLE;
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = USERS_ROLE_ADD_TOASTER_SUCCESS;

        header("location: ../roles/list/");
        die();
    }


    public function adminRolesEdit($id): void
    {
        $role = new rolesModel();
        $role->fetchRole($id);

        $permissions = new permissionsModel();
        $permissionsList = $permissions->getPermissions();


        view('users', 'roles.edit.admin', ["role" => $role,
            "permissionsList" => $permissionsList], 'admin');
    }

    #[NoReturn] public function adminRolesEditPost($id): void
    {
        usersController::isUserHasPermission("users.roles");

        $role = new rolesModel();
        $role->roleName = filter_input(INPUT_POST, "name");
        $role->roleDescription = filter_input(INPUT_POST, "description");
        $role->permList = $_POST['perms'];
        $role->roleWeight = filter_input(INPUT_POST, "weight");

        $role->roleId = $id;
        $role->updateRole();

        //Todo Try to remove that
        $_SESSION['toaster'][0]['title'] = USERS_TOASTER_TITLE;
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = USERS_ROLE_EDIT_TOASTER_SUCCESS;

        header("location: ../list/");
        die();
    }

    #[NoReturn] public function adminRolesDelete($id): void
    {
        usersController::isUserHasPermission("users.roles");

        $role = new rolesModel();
        $role->deleteRole($id);

        //Todo Try to remove that
        $_SESSION['toaster'][0]['title'] = USERS_TOASTER_TITLE;
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = USERS_ROLE_EDIT_TOASTER_SUCCESS;

        header("location: ../list/");
        die();
    }
}
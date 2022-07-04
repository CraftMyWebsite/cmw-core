<?php

namespace CMW\Controller\Roles;

use CMW\Controller\coreController;
use CMW\Controller\Users\usersController;
use CMW\Model\Permissions\permissionsModel;
use CMW\Model\Roles\rolesModel;
use CMW\Model\Users\usersModel;
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


    private usersModel $userModel;
    private rolesModel $roleModel;
    private permissionsModel $permissionsModel;


    public function __construct($theme_path = null)
    {
        parent::__construct($theme_path);
        $this->userModel = new usersModel();
        $this->roleModel = new rolesModel();
        $this->permissionsModel = new permissionsModel();
    }
    
    public function adminRolesList(): void
    {
        usersController::isUserHasPermission("users.roles");

        $rolesList = $this->roleModel->fetchAll();

        view('users', 'roles.list.admin', ["rolesList" => $rolesList], 'admin');
    }


    public function adminRolesAdd(): void
    {
        usersController::isUserHasPermission("users.roles");

        $permissionsList = $this->permissionsModel->getPermissions();

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
        $rm = new rolesModel();
        $role = $this->roleModel->getRoleById($id);

        $permissionsList = $this->permissionsModel->getPermissions();


        view('users', 'roles.edit.admin', ["role" => $role,
            "permissionsList" => $permissionsList, "rm" => $rm], 'admin');
    }

    #[NoReturn] public function adminRolesEditPost($id): void
    {
        usersController::isUserHasPermission("users.roles");


        $roleName = filter_input(INPUT_POST, "name");
        $roleDescription = filter_input(INPUT_POST, "description");
        $permList = $_POST['perms'];
        $roleWeight = filter_input(INPUT_POST, "weight");

        $this->roleModel->updateRole($roleName, $roleDescription, $id, $roleWeight, $permList);

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

        $this->roleModel->deleteRole($id);

        //Todo Try to remove that
        $_SESSION['toaster'][0]['title'] = USERS_TOASTER_TITLE;
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = USERS_ROLE_EDIT_TOASTER_SUCCESS;

        header("location: ../list/");
        die();
    }
}
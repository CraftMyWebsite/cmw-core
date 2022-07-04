<?php

namespace CMW\Controller\Roles;

use CMW\Controller\CoreController;
use CMW\Controller\Users\UsersController;
use CMW\Model\Permissions\PermissionsModel;
use CMW\Model\Roles\RolesModel;
use CMW\Model\Users\UsersModel;
use JetBrains\PhpStorm\NoReturn;
use JsonException;

/**
 * Class: @rolesController
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class RolesController extends CoreController
{


    private UsersModel $userModel;
    private RolesModel $roleModel;
    private PermissionsModel $permissionsModel;


    public function __construct($theme_path = null)
    {
        parent::__construct($theme_path);
        $this->userModel = new UsersModel();
        $this->roleModel = new RolesModel();
        $this->permissionsModel = new PermissionsModel();
    }
    
    public function adminRolesList(): void
    {
        UsersController::isUserHasPermission("users.roles");

        $rolesList = $this->roleModel->fetchAll();

        view('users', 'roles.list.admin', ["rolesList" => $rolesList], 'admin');
    }


    public function adminRolesAdd(): void
    {
        UsersController::isUserHasPermission("users.roles");

        $permissionsList = $this->permissionsModel->getPermissions();

        view('users', 'roles.add.admin', ["permissionsList" => $permissionsList], 'admin');
    }

    #[NoReturn] public function adminRolesAddPost(): void
    {
        UsersController::isUserHasPermission("users.roles");

        $role = new RolesModel();
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
        $rm = new RolesModel();
        $role = $this->roleModel->getRoleById($id);

        $permissionsList = $this->permissionsModel->getPermissions();


        view('users', 'roles.edit.admin', ["role" => $role,
            "permissionsList" => $permissionsList, "rm" => $rm], 'admin');
    }

    #[NoReturn] public function adminRolesEditPost($id): void
    {
        UsersController::isUserHasPermission("users.roles");


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
        UsersController::isUserHasPermission("users.roles");

        $this->roleModel->deleteRole($id);

        //Todo Try to remove that
        $_SESSION['toaster'][0]['title'] = USERS_TOASTER_TITLE;
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = USERS_ROLE_EDIT_TOASTER_SUCCESS;

        header("location: ../list/");
        die();
    }
}
<?php

namespace CMW\Controller\Roles;

use CMW\Controller\CoreController;
use CMW\Controller\Permissions\PermissionsController;
use CMW\Controller\Users\UsersController;
use CMW\Model\Permissions\PermissionsModel;
use CMW\Model\Roles\RolesModel;
use CMW\Model\Users\UsersModel;
use CMW\Router\Link;
use CMW\Utils\View;
use JetBrains\PhpStorm\NoReturn;

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

    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin/roles")]
    #[Link("/list", Link::GET, [], "/cmw-admin/roles")]
    public function adminRolesList(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.roles");

        $rolesList = $this->roleModel->getRoles();

        View::createAdminView("users", "roles.list")->addVariable("rolesList", $rolesList)
            ->view();
    }

    #[Link("/add", Link::GET, [], "/cmw-admin/roles")]
    public function adminRolesAdd(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.roles");

        $permissionController = new PermissionsController();
        $permissionModel = new PermissionsModel();

        View::createAdminView("users", "roles.add")->addVariableList(array(
            "permissionController" => $permissionController,
            "permissionModel" => $permissionModel
        ))
            ->view();
    }

    #[Link("/add", Link::POST, [], "/cmw-admin/roles")]
    #[NoReturn] public function adminRolesAddPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.roles");

        $role = new RolesModel();
        $roleName = filter_input(INPUT_POST, "name");
        $roleDescription = filter_input(INPUT_POST, "description");
        $permList = $_POST['perms'];
        $roleWeight = filter_input(INPUT_POST, "weight", FILTER_SANITIZE_NUMBER_INT);
        $role->createRole($roleName, $roleDescription, $roleWeight, $permList);


        $_SESSION['toaster'][0]['title'] = USERS_TOASTER_TITLE;
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = USERS_ROLE_ADD_TOASTER_SUCCESS;

        header("location: ../roles/list/");
    }

    #[Link("/edit/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/roles")]
    public function adminRolesEdit(int $id): void
    {
        $roleModel = new RolesModel();
        $role = $this->roleModel->getRoleById($id);
        $permissionController = new PermissionsController();
        $permissionModel = new PermissionsModel();


        View::createAdminView("users", "roles.edit")->addVariableList(array(
            "permissionController" => $permissionController,
            "permissionModel" => $permissionModel,
            "roleModel" => $roleModel,
            "role" => $role
        ))
            ->view();
    }

    #[Link("/edit/:id", Link::POST, ["id" => "[0-9]+"], "/cmw-admin/roles")]
    #[NoReturn] public function adminRolesEditPost(int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.roles");

        $roleName = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
        $roleDescription = filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING);
        $permList = $_POST['perms'];
        $roleWeight = filter_input(INPUT_POST, "weight", FILTER_SANITIZE_NUMBER_INT);

        $this->roleModel->updateRole($roleName, $roleDescription, $id, $roleWeight, $permList);

        //Todo Try to remove that
        $_SESSION['toaster'][0]['title'] = USERS_TOASTER_TITLE;
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = USERS_ROLE_EDIT_TOASTER_SUCCESS;

        header("location: ../list/");
    }

    #[Link("/delete/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/roles")]
    #[NoReturn] public function adminRolesDelete(int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.roles");

        $this->roleModel->deleteRole($id);

        //Todo Try to remove that
        $_SESSION['toaster'][0]['title'] = USERS_TOASTER_TITLE;
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = USERS_ROLE_EDIT_TOASTER_SUCCESS;

        header("location: ../list/");
    }
}
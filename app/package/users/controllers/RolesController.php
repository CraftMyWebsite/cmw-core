<?php

namespace CMW\Controller\Users;

use CMW\Controller\Core\CoreController;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Users\PermissionsModel;
use CMW\Model\Users\RolesModel;
use CMW\Model\Users\UsersModel;
use CMW\Router\Link;
use CMW\Utils\Response;
use CMW\Utils\Utils;
use CMW\Manager\Views\View;
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


    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UsersModel();
        $this->roleModel = new RolesModel();
        $this->permissionsModel = new PermissionsModel();
    }

    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin/roles")]
    #[Link("/manage", Link::GET, [], "/cmw-admin/roles")]
    public function adminRolesManage(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.roles.manage");

        $rolesList = $this->roleModel->getRoles();
        $permissionController = new PermissionsController();
        $permissionModel = new PermissionsModel();
        $rolesModel = new RolesModel();

        //Try to improve that ?
        require_once(Utils::getEnv()->getValue("DIR") . "app/package/users/functions/loadPermissions.php");


        View::createAdminView("users", "roles")
            ->addScriptBefore("app/package/users/views/assets/js/manageRoles.js",
                "admin/resources/vendors/iziToast/iziToast.min.js",
                "app/package/users/views/assets/js/rolesWeights.js")
            ->addStyle("admin/resources/vendors/iziToast/iziToast.min.css")
            ->addVariableList(["rolesList" => $rolesList, "permissionController" => $permissionController,
                "permissionModel" => $permissionModel, "rolesModel" => $rolesModel])
            ->view();
    }

    #[Link("/add", Link::GET, [], "/cmw-admin/roles")]
    public function adminRolesAdd(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.roles.add");

        $permissionController = new PermissionsController();
        $permissionModel = new PermissionsModel();

        //Try to improve that ?
        require_once(getenv("DIR") . "app/package/users/functions/loadPermissions.php");


        View::createAdminView("users", "roles.add")->addVariableList(array(
            "permissionController" => $permissionController,
            "permissionModel" => $permissionModel
        ))
            ->view();
    }

    #[Link("/add", Link::POST, [], "/cmw-admin/roles")]
    #[NoReturn] public function adminRolesAddPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.roles.add");

        $role = new RolesModel();
        $roleName = filter_input(INPUT_POST, "name");
        $roleDescription = filter_input(INPUT_POST, "description");
        $permList = $_POST['perms'];
        $roleWeight = filter_input(INPUT_POST, "weight", FILTER_SANITIZE_NUMBER_INT);
        $role->createRole($roleName, $roleDescription, $roleWeight, $permList);

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate('users.toaster_role_added'));

        header("location: " . $_SERVER['HTTP_REFERER']);
    }

    #[Link("/edit/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/roles")]
    public function adminRolesEdit(int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.roles.edit");

        $roleModel = new RolesModel();
        $role = $this->roleModel->getRoleById($id);
        $permissionController = new PermissionsController();
        $permissionModel = new PermissionsModel();

        //Try to improve that ?
        require_once(Utils::getEnv()->getValue("DIR") . "app/package/users/functions/loadPermissions.php");

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
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.roles.edit");

        $roleName = filter_input(INPUT_POST, "name");
        $roleDescription = filter_input(INPUT_POST, "description");
        $permList = $_POST['perms'];
        $roleWeight = filter_input(INPUT_POST, "weight", FILTER_SANITIZE_NUMBER_INT);

        $this->roleModel->updateRole($roleName, $roleDescription, $id, $roleWeight, $permList);

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate('users.toaster_role_edited'));


        header("location: " . $_SERVER['HTTP_REFERER']);
    }

    #[Link("/delete/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/roles")]
    #[NoReturn] public function adminRolesDelete(int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.roles.delete");

        $this->roleModel->deleteRole($id);

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate('users.toaster_role_deleted'));


        header("location: " . $_SERVER['HTTP_REFERER']);
    }

    #[Link("/getRole/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/roles")]
    public function admingetRole(int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.roles.manage");

        $_SESSION['editRoleId'] = $id;

        $role = (new RolesModel())->getRoleById($id);

        $rolePermissions = [];

        foreach ($role?->getPermissions() as $permission) {
            if ($permission->hasParent()) {
                $rolePermissions[$permission->getId()] = $permission->getParent()?->getCode();
            }
            $rolePermissions[$permission->getId()] = $permission->getCode();
        }

        $data = [
            "id" => $role?->getId(),
            "name" => $role?->getName(),
            "weight" => $role?->getWeight(),
            "description" => $role?->getDescription(),
            "permissions" => $rolePermissions
        ];

        try {
            print_r(json_encode($data, JSON_THROW_ON_ERROR));
        } catch (JsonException) {
            print("ERROR");
        }
    }

    #[Link("/getRoles", Link::GET, [], "/cmw-admin/roles")]
    public function admingGetRoles(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.roles.manage");

        $roles = $this->roleModel->getRoles();

        $rolePermissions = [];
        $data = [];

        foreach ($roles as $role):

            foreach ($role?->getPermissions() as $permission) {
                if ($permission->hasParent()) {
                    $rolePermissions[$permission->getId()] = $permission->getParent()?->getCode();
                }
                $rolePermissions[$permission->getId()] = $permission->getCode();
            }

            $data[$role?->getId()] = [
                "name" => $role?->getName(),
                "weight" => $role?->getWeight(),
                "description" => $role?->getDescription(),
                "permissions" => $rolePermissions
            ];

        endforeach;

        try {
            print_r(json_encode($data, JSON_THROW_ON_ERROR));
        } catch (JsonException) {
            print("ERROR");
        }
    }

}
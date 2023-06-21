<?php

namespace CMW\Controller\Users;

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Users\PermissionsModel;
use CMW\Model\Users\RolesModel;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;
use JsonException;

/**
 * Class: @rolesController
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class RolesController extends AbstractController
{

    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin/roles")]
    #[Link("/manage", Link::GET, [], "/cmw-admin/roles")]
    private function adminRolesManage(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.roles.manage");

        $rolesList = RolesModel::getInstance()->getRoles();
        $permissionController = PermissionsController::getInstance();
        $permissionModel = PermissionsModel::getInstance();
        $rolesModel = RolesModel::getInstance();

        //Todo Try to improve that ?
        require_once(EnvManager::getInstance()->getValue("DIR") . "App/Package/Users/Functions/loadPermissions.php");


        View::createAdminView("Users", "roles")
            ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css",
                "Admin/Resources/Assets/Css/Pages/simple-datatables.css",
                "Admin/Resources/Vendors/Izitoast/iziToast.min.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/Umd/simple-datatables.js",
                "Admin/Resources/Assets/Js/Pages/simple-datatables.js",
                "App/Package/Users/Views/Assets/Js/rolesWeights.js",
                "Admin/Resources/Vendors/Izitoast/iziToast.min.js")
            ->addVariableList(["rolesList" => $rolesList, "permissionController" => $permissionController,
                "permissionModel" => $permissionModel, "rolesModel" => $rolesModel])
            ->view();
    }

    #[Link("/add", Link::GET, [], "/cmw-admin/roles")]
    private function adminRolesAdd(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.roles.add");

        $permissionController = PermissionsController::getInstance();
        $permissionModel = PermissionsModel::getInstance();

        //Todo Try to improve that ?
        require_once(getenv("DIR") . "App/Package/users/functions/loadPermissions.php");


        View::createAdminView("Users", "roles.add")->addVariableList(array(
            "permissionController" => $permissionController,
            "permissionModel" => $permissionModel
        ))
            ->view();
    }

    #[Link("/add", Link::POST, [], "/cmw-admin/roles")]
    #[NoReturn] private function adminRolesAddPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.roles.add");

        $role = RolesModel::getInstance();
        $roleName = filter_input(INPUT_POST, "name");
        $roleDescription = filter_input(INPUT_POST, "description");
        $permList = $_POST['perms']; //todo need to secure that !
        $roleWeight = filter_input(INPUT_POST, "weight", FILTER_SANITIZE_NUMBER_INT);
        $roleIsDefault = isset($_POST['isDefault']) ? 1 : 0;
        $role->createRole($roleName, $roleDescription, $roleWeight, $roleIsDefault, $permList);

        Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate('users.toaster_role_added'));

        Redirect::redirectPreviousRoute();
    }

    #[Link("/edit/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/roles/manage")]
    private function adminRolesEdit(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.roles.edit");

        $roleModel = RolesModel::getInstance();
        $role = RolesModel::getInstance()->getRoleById($id);
        $permissionController = new PermissionsController();
        $permissionModel = new PermissionsModel();

        //Todo Try to improve that ?
        require_once(EnvManager::getInstance()->getValue("DIR") . "App/Package/Users/Functions/loadPermissions.php");

        View::createAdminView("Users", "roles.edit")->addVariableList(array(
            "permissionController" => $permissionController,
            "permissionModel" => $permissionModel,
            "roleModel" => $roleModel,
            "role" => $role
        ))
            ->view();
    }

    #[Link("/edit/:id", Link::POST, ["id" => "[0-9]+"], "/cmw-admin/roles/manage")]
    #[NoReturn] private function adminRolesEditPost(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.roles.edit");

        $roleName = filter_input(INPUT_POST, "name");
        $roleDescription = filter_input(INPUT_POST, "description");
        $permList = $_POST['perms'];
        $roleWeight = filter_input(INPUT_POST, "weight", FILTER_SANITIZE_NUMBER_INT);

        RolesModel::getInstance()->updateRole($roleName, $roleDescription, $id, $roleWeight, $permList);

        Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate('users.toaster.role_edited'));


        Redirect::redirectPreviousRoute();
    }

    #[Link("/set_default/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/roles")]
    #[NoReturn] private function adminRolesSetDefault(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.roles.edit");

        RolesModel::getInstance()->changeDefault($id);

        Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate('users.toaster.role_edited'));

        Redirect::redirectPreviousRoute();
    }

    #[Link("/delete/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/roles")]
    #[NoReturn] private function adminRolesDelete(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.roles.delete");

        $isDefault = RolesModel::getInstance()->roleIsDefault($id);

        if ($isDefault) {
            Flash::send(Alert::ERROR,"Erreur", "Vous ne pouvez pas supprimé le rôle par defaut");
            Redirect::redirectPreviousRoute();

        }

        RolesModel::getInstance()->deleteRole($id);

        Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate('users.toaster_role_deleted'));


        Redirect::redirectPreviousRoute();
    }

    #[Link("/getRole/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/roles")]
    private function adminGetRole(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.roles.manage");

        $_SESSION['editRoleId'] = $id;

        $role = (RolesModel::getInstance())->getRoleById($id);

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
    private function adminGetRoles(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.roles.manage");

        $roles = RolesModel::getInstance()->getRoles();

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
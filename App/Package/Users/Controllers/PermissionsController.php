<?php

namespace CMW\Controller\Users;

use CMW\Controller\Core\PackageController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Model\Users\PermissionsModel;
use CMW\Utils\Redirect;
use JsonException;

/**
 * Class: @permissionsController
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class PermissionsController extends AbstractController
{

    /**
     * @return \CMW\Entity\Users\PermissionEntity[]
     */
    public function getParents(): array
    {
        return PermissionsModel::getInstance()->getParents();
    }

    #[Link("/permissions/import", Link::GET, [], "/cmw-admin/roles")]
    private function adminImportPermissions(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.roles.manage");

        if ($this->loadPackagesPermissions()){
            Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
                LangManager::translate("users.toaster.load_permissions_success"));
        }
        Redirect::redirectPreviousRoute();
    }

    public function loadPackagesPermissions(): bool
    {
        $packages = PackageController::getAllPackages();

        foreach ($packages as $package):
            $packageName = $package->name();

            $initFolder = EnvManager::getInstance()->getValue("dir") . "App/Package/$packageName/Init";

            if (!is_dir($initFolder)) {
                continue;
            }

            $initFiles = array_diff(scandir($initFolder), ['..', '.']);

            if (empty($initFiles)) {
                continue;
            }

            // Load permissions files
            $permissionFile = "$initFolder/permissions.json";

            if (file_exists($permissionFile)) {

                try {
                    $permissions = json_decode(file_get_contents($permissionFile), false, 512, JSON_THROW_ON_ERROR);

                    foreach ($permissions as $permission) {
                        PermissionsModel::getInstance()->addFullCodePermission($permission);
                    }
                } catch (JsonException $_) {
                    Flash::send(Alert::WARNING, LangManager::translate("core.toaster.warning"),
                        LangManager::translate("users.toaster.load_permissions_error", ['package' => $packageName]));
                    return false;
                }

            }

        endforeach;

        return true;
    }


}
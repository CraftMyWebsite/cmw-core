<?php

namespace CMW\Controller\Users;

use CMW\Controller\Core\CoreController;
use CMW\Entity\Users\UserSettingsEntity;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Router\Link;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Manager\Views\View;
use CMW\Model\Users\UsersSettingsModel;
use CMW\Utils\EnvManager;
use CMW\Utils\Response;
use CMW\Utils\Utils;
use JsonException;

/**
 * Class: @UsersSettingsController
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class UsersSettingsController extends CoreController
{

    public static function getDefaultImageLink(): string
    {
        return EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . "Public/Uploads/Users/Default/defaultImage.jpg";
    }

    /**
     * @throws \CMW\Manager\Router\RouterException
     */
    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin/users")]
    #[Link("/settings", Link::GET, [], "/cmw-admin/users")]
    public function settings(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.settings");

        View::createAdminView("Users", "settings")
            ->addVariableList(["settings" => new UserSettingsEntity()])
            ->view();
    }

    #[Link("/settings", Link::POST, [], "/cmw-admin/users")]
    public function settingsPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.settings");

        if($_FILES['defaultPicture']['name'] !== "" ) {
            $defaultPicture = $_FILES['defaultPicture'];

            try {
                $newDefaultImage = ImagesManager::upload($defaultPicture, "users/Default");
                UsersSettingsModel::updateSetting("defaultImage", $newDefaultImage);
            } catch (JsonException $e) {
                Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                    LangManager::translate("core.toaster.internalError") . " => $e");
            }

        }

        [$resetPasswordMethod, $profilePage] = Utils::filterInput("reset_password_method", "profile_page");

        UsersSettingsModel::updateSetting("resetPasswordMethod", $resetPasswordMethod);
        UsersSettingsModel::updateSetting("profilePage", $profilePage);

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("core.toaster.config.success"));

        header("Location: settings");
    }

}
<?php

namespace CMW\Controller\Users;

use CMW\Controller\Core\CoreController;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Users\UsersModel;
use CMW\Model\Users\UsersSettingsModel;
use CMW\Router\Link;
use CMW\Utils\Images;
use CMW\Utils\Response;
use CMW\Utils\Utils;
use CMW\Utils\View;

/**
 * Class: @UsersSettingsController
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class UsersSettingsController extends CoreController
{
    private UsersSettingsModel $settingsModel;

    public function __construct()
    {
        parent::__construct();
        $this->settingsModel = new UsersSettingsModel();
    }

    /**
     * @throws \CMW\Router\RouterException
     */
    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin/users")]
    #[Link("/settings", Link::GET, [], "/cmw-admin/users")]
    public function settings(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.settings");


        View::createAdminView("users", "settings")
            ->addVariableList(["settings" => $this->settingsModel])
            ->view();
    }

    #[Link("/settings", Link::POST, [], "/cmw-admin/users")]
    public function settingsPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.settings");

        if($_FILES['defaultPicture']['name'] !== "" ) {
            $defaultPicture = $_FILES['defaultPicture'];

            $newDefaultImage = Images::upload($defaultPicture, "users/default");

            UsersSettingsModel::updateSetting("defaultImage", $newDefaultImage);
        }

        [$resetPasswordMethod] = Utils::filterInput("resetPasswordMethod");

        UsersSettingsModel::updateSetting("resetPasswordMethod", $resetPasswordMethod);

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("core.toaster.config.success"));

        header("Location: settings");
    }

}
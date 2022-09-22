<?php

namespace CMW\Controller\Users;

use CMW\Controller\Core\CoreController;
use CMW\Model\Users\UsersModel;
use CMW\Model\Users\UsersSettingsModel;
use CMW\Router\Link;
use CMW\Utils\Images;
use CMW\Utils\View;

/**
 * Class: @UsersSettingsController
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class UsersSettingsController extends CoreController
{
    /**
     * @throws \CMW\Router\RouterException
     */
    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin/users")]
    #[Link("/settings", Link::GET, [], "/cmw-admin/users")]
    public function settings(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.settings");


        View::createAdminView("users", "settings")
            ->view();
    }

    #[Link("/settings", Link::POST, [], "/cmw-admin/users")]
    public function settingsPost(): void
    {
        $defaultPicture = $_FILES['defaultPicture'];

        $newDefaultImage = Images::upload($defaultPicture, "users/default");

        UsersSettingsModel::updateSetting("defaultImage", $newDefaultImage);

        header("Location: settings");
    }

}
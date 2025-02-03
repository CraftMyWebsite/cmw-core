<?php

namespace CMW\Controller\Users;

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Package\AbstractController;
use CMW\Model\Users\UsersSettingsModel;

/**
 * Class: @UsersSettingsController
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @deprecated
 */
class UsersSettingsController extends AbstractController
{
    /**
     * @return string
     * @deprecated
     */
    public static function getDefaultImageLink(): string
    {
        $defaultImg = UsersSettingsModel::getInstance()->getSetting('defaultImage');
        return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Public/Uploads/Users/Default/' . $defaultImg;
    }
}

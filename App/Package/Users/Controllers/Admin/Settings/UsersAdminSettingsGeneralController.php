<?php

namespace CMW\Controller\Users\Admin\Settings;

use CMW\Controller\Users\UsersController;
use CMW\Entity\Users\Settings\BulkSettingsEntity;
use CMW\Entity\Users\UserSettingsEntity;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Manager\Views\View;
use CMW\Model\Users\UsersSettingsModel;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @UsersAdminSettingsGeneralController
 * @package Users
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/controllers
 */
class UsersAdminSettingsGeneralController extends AbstractController
{
    #[Link('/settings/general', Link::GET, [], '/cmw-admin/users')]
    private function settings(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'users.settings');

        View::createAdminView('Users', 'Settings/general')
            ->addVariableList(['settings' => UserSettingsEntity::getInstance()])
            ->view();
    }

    #[NoReturn] #[Link('/settings/general', Link::POST, [], '/cmw-admin/users')]
    private function settingsPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'users.settings');

        $profilePage = FilterManager::filterInputStringPost('profile_page');

        $settingsStatus = UsersSettingsModel::getInstance()->bulkUpdateSettings(
            new BulkSettingsEntity('profilePage', $profilePage),
        );

        if (!$settingsStatus) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.config.error'),
            );
        } else {
            Flash::send(
                Alert::SUCCESS,
                LangManager::translate('core.toaster.success'),
                LangManager::translate('core.toaster.config.success'),
            );
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/settings/general/image/reset', Link::GET, [], '/cmw-admin/users')]
    private function resetDefaultImg(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'users.settings');

        if (!UsersSettingsModel::getInstance()->updateSetting('defaultImage', 'defaultImage.jpg')) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.config.error'),
            );
        } else {

            Flash::send(
                Alert::SUCCESS,
                LangManager::translate('core.toaster.success'),
                LangManager::translate('core.toaster.config.success'),
            );
        }
        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/settings/general/image', Link::POST, [], '/cmw-admin/users')]
    private function settingsImagePost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'users.settings');

        if (!isset($_FILES['defaultPicture']) || $_FILES['defaultPicture']['error'] !== 0) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.config.error'),
            );
            Redirect::redirectPreviousRoute();
        }

        $defaultPicture = $_FILES['defaultPicture'];

        $newDefaultImage = ImagesManager::convertAndUpload($defaultPicture, 'Users/Default');
        if (!UsersSettingsModel::getInstance()->updateSetting('defaultImage', $newDefaultImage)) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.config.error'),
            );
        } else {
            Flash::send(
                Alert::SUCCESS,
                LangManager::translate('core.toaster.success'),
                LangManager::translate('core.toaster.config.success'),
            );
        }

        Redirect::redirectPreviousRoute();
    }
}

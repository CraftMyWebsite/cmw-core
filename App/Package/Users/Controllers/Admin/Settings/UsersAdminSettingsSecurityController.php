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
use CMW\Manager\Views\View;
use CMW\Model\Users\RolesModel;
use CMW\Model\Users\UsersSettingsModel;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @UsersAdminSettingsSecurityController
 * @package Users
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/controllers
 */
class UsersAdminSettingsSecurityController extends AbstractController
{
    #[Link('/settings/security', Link::GET, [], '/cmw-admin/users')]
    private function settings(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'users.settings');

        $roles = RolesModel::getInstance()->getRoles();
        $settings = UserSettingsEntity::getInstance();

        View::createAdminView('Users', 'Settings/security')
            ->addVariableList(['settings' => $settings, 'roles' => $roles])
            ->view();
    }

    #[NoReturn] #[Link('/settings/security', Link::POST, [], '/cmw-admin/users')]
    private function settingsPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'users.settings');

        $resetPasswordMethod = FilterManager::filterInputStringPost('reset_password_method');
        $securityReinforced = FilterManager::filterInputStringPost('security_reinforced');

        $settingsStatus = UsersSettingsModel::getInstance()->bulkUpdateSettings(
            new BulkSettingsEntity('resetPasswordMethod', $resetPasswordMethod),
            new BulkSettingsEntity('securityReinforced', $securityReinforced)
        );

        if (!$settingsStatus) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.config.error'),
            );
            Redirect::redirectPreviousRoute();
        }

        $listEnforcedToggle = FilterManager::filterInputStringPost('listEnforcedToggle');

        if ($listEnforcedToggle === '1') {
            if (empty($_POST['enforcedRoles'])) {
                $listEnforcedToggle = 0;
                if (!UsersSettingsModel::getInstance()->clearEnforcedRoles()) {
                    Flash::send(Alert::ERROR, 'Erreur', 'Impossible de mettre à jour les rôles imposés en 2fa !');
                    Redirect::redirectPreviousRoute();
                }
            } else {
                if (UsersSettingsModel::getInstance()->clearEnforcedRoles()) {
                    foreach ($_POST['enforcedRoles'] as $roleId) {
                        UsersSettingsModel::getInstance()->updateEnforcedRoles($roleId);
                    }
                } else {
                    Flash::send(Alert::ERROR, 'Erreur', 'Impossible de mettre à jour les rôles imposés en 2fa !');
                    Redirect::redirectPreviousRoute();
                }
            }
        } else {
            if (!UsersSettingsModel::getInstance()->clearEnforcedRoles()) {
                Flash::send(Alert::ERROR, 'Erreur', 'Impossible de mettre à jour les rôles imposés en 2fa !');
                Redirect::redirectPreviousRoute();
            }
        }

        UsersSettingsModel::getInstance()->updateSetting('listEnforcedToggle', $listEnforcedToggle);

        Flash::send(
            Alert::SUCCESS,
            LangManager::translate('core.toaster.success'),
            LangManager::translate('core.toaster.config.success'),
        );

        Redirect::redirectPreviousRoute();
    }
}

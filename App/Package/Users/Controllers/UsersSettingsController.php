<?php

namespace CMW\Controller\Users;

use CMW\Entity\Users\UserSettingsEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Uploads\ImagesException;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Manager\Views\View;
use CMW\Model\Users\RolesModel;
use CMW\Model\Users\UsersSettingsModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;
use JsonException;

/**
 * Class: @UsersSettingsController
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 0.0.1
 */
class UsersSettingsController extends AbstractController
{
    public static function getDefaultImageLink(): string
    {
        $defaultImg = UsersSettingsModel::getSetting('defaultImage');
        return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Public/Uploads/Users/Default/' . $defaultImg;
    }

    /**
     * @throws \CMW\Manager\Router\RouterException
     */
    #[Link(path: '/', method: Link::GET, scope: '/cmw-admin/users')]
    #[Link('/settings', Link::GET, [], '/cmw-admin/users')]
    private function settings(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'users.settings');

        $roles = RolesModel::getInstance()->getRoles();
        $blacklistedPseudo = UsersSettingsModel::getInstance()->getBlacklistedPseudos();

        View::createAdminView('Users', 'settings')
            ->addStyle('Admin/Resources/Assets/Css/simple-datatables.css')
            ->addScriptAfter('Admin/Resources/Vendors/Simple-datatables/simple-datatables.js',
                'Admin/Resources/Vendors/Simple-datatables/config-datatables.js',
                'App/Package/Users/Views/Assets/Js/rolesWeights.js')
            ->addVariableList(['settings' => new UserSettingsEntity(), 'roles' => $roles, 'pseudos' => $blacklistedPseudo])
            ->view();
    }

    #[Link('/settings/resetImg', Link::GET, [], '/cmw-admin/users')]
    private function resetDefaultImg(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'users.settings');

        UsersSettingsModel::updateSetting('defaultImage', 'defaultImage.jpg');

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('core.toaster.config.success'));

        Redirect::redirectPreviousRoute();
    }

    #[Link('/settings', Link::POST, [], '/cmw-admin/users')]
    private function settingsPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'users.settings');

        if ($_FILES['defaultPicture']['name'] !== '') {
            $defaultPicture = $_FILES['defaultPicture'];

            try {
                $newDefaultImage = ImagesManager::convertAndUpload($defaultPicture, 'Users/Default');
                UsersSettingsModel::updateSetting('defaultImage', $newDefaultImage);
            } catch (ImagesException $e) {
                Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                    LangManager::translate('core.errors.upload.image') . ' => ' . $e->getMessage());
                Redirect::redirectPreviousRoute();
            }
        }

        [$resetPasswordMethod, $profilePage] = Utils::filterInput('reset_password_method', 'profile_page');

        UsersSettingsModel::updateSetting('resetPasswordMethod', $resetPasswordMethod);
        UsersSettingsModel::updateSetting('profilePage', $profilePage);

        [$listEnforcedToggle] = Utils::filterInput('listEnforcedToggle');

        if ($listEnforcedToggle === '1') {
            if (empty($_POST['enforcedRoles'])) {
                $listEnforcedToggle = 0;
                if (!UsersSettingsModel::getInstance()->clearEnforcedRoles()) {
                    Flash::send(Alert::ERROR, 'Erreur', 'Impossible de mettre à jour les rôles imposer en 2fa !');
                    Redirect::redirectPreviousRoute();
                }
            } else {
                if (UsersSettingsModel::getInstance()->clearEnforcedRoles()) {
                    foreach ($_POST['enforcedRoles'] as $roleId) {
                        UsersSettingsModel::getInstance()->updateEnforcedRoles($roleId);
                    }
                } else {
                    Flash::send(Alert::ERROR, 'Erreur', 'Impossible de mettre à jour les rôles imposer en 2fa !');
                    Redirect::redirectPreviousRoute();
                }
            }
        } else {
            if (!UsersSettingsModel::getInstance()->clearEnforcedRoles()) {
                Flash::send(Alert::ERROR, 'Erreur', 'Impossible de mettre à jour les rôles imposer en 2fa !');
                Redirect::redirectPreviousRoute();
            }
        }

        UsersSettingsModel::updateSetting('listEnforcedToggle', $listEnforcedToggle);

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('core.toaster.config.success'));

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/settings/blacklist/pseudo', Link::POST, [], '/cmw-admin/users')]
    private function pseudoBlacklistPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'users.settings.blacklist.add');

        if (empty($_POST['pseudo'])) {
            Redirect::redirectPreviousRoute();
        }

        $pseudo = filter_input(INPUT_POST, 'pseudo');

        if (UsersSettingsModel::getInstance()->addBlacklistedPseudo($pseudo)) {
            Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
                LangManager::translate('users.settings.blacklisted.pseudo.toasters.add.success', ['pseudo' => $pseudo]));
        } else {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('users.settings.blacklisted.pseudo.toasters.add.error', ['pseudo' => $pseudo]));
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/settings/blacklist/pseudo/edit/:id', Link::POST, ['id' => '[0-9]+'], '/cmw-admin/users')]
    private function editPseudoBlacklistPost(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'users.settings.blacklist.edit');

        if (empty($_POST['pseudo'])) {
            Redirect::redirectPreviousRoute();
        }

        $pseudo = filter_input(INPUT_POST, 'pseudo');

        if (UsersSettingsModel::getInstance()->editBlacklistedPseudo($id, $pseudo)) {
            Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
                LangManager::translate('users.settings.blacklisted.pseudo.toasters.edit.success', ['pseudo' => $pseudo]));
        } else {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('users.settings.blacklisted.pseudo.toasters.edit.error', ['pseudo' => $pseudo]));
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/settings/blacklist/pseudo/delete/:id', Link::GET, ['id' => '[0-9]+'], '/cmw-admin/users')]
    private function deletePseudoBlacklisted(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'users.settings.blacklist.delete');

        if (UsersSettingsModel::getInstance()->removeBlacklistedPseudo($id)) {
            Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
                LangManager::translate('users.settings.blacklisted.pseudo.toasters.delete.success'));
        } else {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('users.settings.blacklisted.pseudo.toasters.delete.error'));
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/settings/blacklist/pseudo/deleteSelected', Link::POST, [], '/cmw-admin/users', secure: false)]
    private function adminDeleteSelectedPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'users.settings.blacklist.delete');

        $selectedIds = $_POST['selectedIds'];

        if (empty($selectedIds)) {
            Flash::send(Alert::ERROR, 'Blacklist', 'Aucun pseudo sélectionné');
            Redirect::redirectPreviousRoute();
        }

        $i = 0;
        foreach ($selectedIds as $selectedId) {
            $selectedId = FilterManager::filterData($selectedId, 11, FILTER_SANITIZE_NUMBER_INT);
            UsersSettingsModel::getInstance()->removeBlacklistedPseudo($selectedId);
            $i++;
        }
        Flash::send(Alert::SUCCESS, 'Blacklist', "$i pseudos supprimé !");

        Redirect::redirectPreviousRoute();
    }
}

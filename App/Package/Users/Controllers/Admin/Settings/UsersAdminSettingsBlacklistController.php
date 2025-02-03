<?php

namespace CMW\Controller\Users\Admin\Settings;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Users\UsersSettingsModel;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;
use function filter_input;
use const FILTER_SANITIZE_NUMBER_INT;
use const INPUT_POST;

/**
 * Class: @UsersAdminSettingsBlacklistController
 * @package Users
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/controllers
 */
class UsersAdminSettingsBlacklistController extends AbstractController
{
    #[Link('/settings/blacklist/pseudo', Link::GET, [], '/cmw-admin/users')]
    private function settings(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'users.settings');

        $blacklistedPseudo = UsersSettingsModel::getInstance()->getBlacklistedPseudos();

        View::createAdminView('Users', 'Settings/blacklist')
            ->addStyle('Admin/Resources/Assets/Css/simple-datatables.css')
            ->addScriptAfter('Admin/Resources/Vendors/Simple-datatables/simple-datatables.js',
                'Admin/Resources/Vendors/Simple-datatables/config-datatables.js')
            ->addVariableList(['pseudos' => $blacklistedPseudo])
            ->view();
    }

    #[NoReturn]
    #[Link('/settings/blacklist/pseudo', Link::POST, [], '/cmw-admin/users')]
    private function pseudoBlacklistPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'users.settings.blacklist.add');

        if (empty($_POST['pseudo'])) {
            Redirect::redirectPreviousRoute();
        }

        $pseudo = FilterManager::filterInputStringPost('pseudo');

        if (UsersSettingsModel::getInstance()->addBlacklistedPseudo($pseudo)) {
            Flash::send(
                Alert::SUCCESS,
                LangManager::translate('core.toaster.success'),
                LangManager::translate('users.settings.blacklisted.pseudo.toasters.add.success', ['pseudo' => $pseudo]),
            );
        } else {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                LangManager::translate('users.settings.blacklisted.pseudo.toasters.add.error', ['pseudo' => $pseudo]),
            );
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
            Flash::send(
                Alert::SUCCESS,
                LangManager::translate('core.toaster.success'),
                LangManager::translate('users.settings.blacklisted.pseudo.toasters.edit.success', ['pseudo' => $pseudo])
            );
        } else {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                LangManager::translate('users.settings.blacklisted.pseudo.toasters.edit.error', ['pseudo' => $pseudo])
            );
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/settings/blacklist/pseudo/delete/:id', Link::GET, ['id' => '[0-9]+'], '/cmw-admin/users')]
    private function deletePseudoBlacklisted(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'users.settings.blacklist.delete');

        if (UsersSettingsModel::getInstance()->removeBlacklistedPseudo($id)) {
            Flash::send(
                Alert::SUCCESS,
                LangManager::translate('core.toaster.success'),
                LangManager::translate('users.settings.blacklisted.pseudo.toasters.delete.success'),
            );
        } else {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                LangManager::translate('users.settings.blacklisted.pseudo.toasters.delete.error'),
            );
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/settings/blacklist/pseudo/delete/bulk', Link::POST, [], '/cmw-admin/users', secure: false)]
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

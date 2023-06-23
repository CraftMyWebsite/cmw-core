<?php

namespace CMW\Controller\Users;

use CMW\Entity\Users\UserSettingsEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Manager\Views\View;
use CMW\Model\Users\UsersSettingsModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;
use JsonException;

/**
 * Class: @UsersSettingsController
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class UsersSettingsController extends AbstractController
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
    private function settings(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.settings");

        View::createAdminView("Users", "settings")
            ->addVariableList(["settings" => new UserSettingsEntity()])
            ->view();
    }

    #[Link("/settings", Link::POST, [], "/cmw-admin/users")]
    private function settingsPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.settings");

        if ($_FILES['defaultPicture']['name'] !== "") {
            $defaultPicture = $_FILES['defaultPicture'];

            try {
                $newDefaultImage = ImagesManager::upload($defaultPicture, "Users/Default");
                UsersSettingsModel::updateSetting("defaultImage", $newDefaultImage);
            } catch (JsonException $e) {
                Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                    LangManager::translate("core.toaster.internalError") . " => $e");
            }

        }

        [$resetPasswordMethod, $profilePage] = Utils::filterInput("reset_password_method", "profile_page");

        UsersSettingsModel::updateSetting("resetPasswordMethod", $resetPasswordMethod);
        UsersSettingsModel::updateSetting("profilePage", $profilePage);

        Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate("core.toaster.config.success"));

        Redirect::redirectPreviousRoute();
    }

    #[Link("/settings/blacklist/pseudo", Link::GET, [], "/cmw-admin/users")]
    private function pseudoBlacklist(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.settings.blacklist.pseudo");

        $blacklistedPseudo = UsersSettingsModel::getInstance()->getBlacklistedPseudos();

        View::createAdminView("Users", "Settings/blacklistPseudo")
            ->addVariableList(["pseudos" => $blacklistedPseudo])
            ->view();
    }

    #[NoReturn] #[Link("/settings/blacklist/pseudo", Link::POST, [], "/cmw-admin/users")]
    private function pseudoBlacklistPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.settings.blacklist.pseudo");

        if (empty($_POST['pseudo'])) {
            Redirect::redirectPreviousRoute();
        }

        $pseudo = filter_input(INPUT_POST, 'pseudo');

        if (UsersSettingsModel::getInstance()->addBlacklistedPseudo($pseudo)) {
            Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
                LangManager::translate('users.settings.blacklisted.pseudo.toasters.add.success', ['pseudo' => $pseudo]));
        } else {
            Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                LangManager::translate('users.settings.blacklisted.pseudo.toasters.add.error', ['pseudo' => $pseudo]));
        }

        Redirect::redirectPreviousRoute();
    }

    #[Link("/settings/blacklist/pseudo/edit/:id", Link::GET, ['id' => "[0-9]+"], "/cmw-admin/users")]
    private function editPseudoBlacklist(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.settings.blacklist.pseudo");

        $blacklistedPseudo = UsersSettingsModel::getInstance()->getBlacklistedPseudo($id);

        View::createAdminView("Users", "Settings/blacklistPseudoEdit")
            ->addVariableList(["pseudo" => $blacklistedPseudo])
            ->view();
    }

    #[NoReturn] #[Link("/settings/blacklist/pseudo/edit/:id", Link::POST, ['id' => "[0-9]+"], "/cmw-admin/users")]
    private function editPseudoBlacklistPost(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.settings.blacklist.pseudo");

        if (empty($_POST['pseudo'])) {
            Redirect::redirectPreviousRoute();
        }

        $pseudo = filter_input(INPUT_POST, 'pseudo');

        if (UsersSettingsModel::getInstance()->editBlacklistedPseudo($id, $pseudo)) {
            Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
                LangManager::translate('users.settings.blacklisted.pseudo.toasters.edit.success', ['pseudo' => $pseudo]));
        } else {
            Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                LangManager::translate('users.settings.blacklisted.pseudo.toasters.edit.error', ['pseudo' => $pseudo]));
        }

        Redirect::redirect('cmw-admin/users/settings/blacklist/pseudo');
    }

    #[NoReturn] #[Link("/settings/blacklist/pseudo/delete/:id", Link::GET, ['id' => "[0-9]+"], "/cmw-admin/users")]
    private function deletePseudoBlacklisted(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "users.settings.blacklist.pseudo");

        if (UsersSettingsModel::getInstance()->removeBlacklistedPseudo($id)) {
            Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
                LangManager::translate('users.settings.blacklisted.pseudo.toasters.delete.success'));
        } else {
            Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                LangManager::translate('users.settings.blacklisted.pseudo.toasters.delete.error'));
        }

        Redirect::redirectPreviousRoute();
    }

}
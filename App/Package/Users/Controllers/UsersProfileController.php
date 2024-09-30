<?php

namespace CMW\Controller\Users;

use CMW\Entity\Users\UserSettingsEntity;
use CMW\Event\Users\DeleteUserAccountEvent;
use CMW\Manager\Events\Emitter;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Security\EncryptManager;
use CMW\Manager\Twofa\TwoFaManager;
use CMW\Manager\Uploads\ImagesException;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Manager\Views\View;
use CMW\Model\Users\UserPictureModel;
use CMW\Model\Users\Users2FaModel;
use CMW\Model\Users\UsersModel;
use CMW\Model\Users\UsersSettingsModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;
use function is_null;
use function mb_strtolower;
use function password_hash;
use function strlen;
use const PASSWORD_BCRYPT;

class UsersProfileController extends AbstractController
{
    /**
     * @throws \CMW\Manager\Router\RouterException
     */
    #[Link('/profile', Link::GET)]
    private function publicProfile(): void
    {
        if (!UsersController::isUserLogged() && !UserSettingsEntity::getInstance()->isProfilePageEnabled()) {
            Redirect::redirect('login');
        }

        if (!UserSettingsEntity::getInstance()->isProfilePageEnabled()) {
            Redirect::redirectToHome();
        }

        if (!UsersController::isUserLogged()) {
            Redirect::redirect('login');
        }

        $user = UsersModel::getCurrentUser();

        if (is_null($user)) {
            Redirect::redirectToHome();
        }

        if (UsersModel::getCurrentUser()?->getId() !== $user->getId()) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'), "Vous ne pouvez pas éditer le profile de quelqu'un d'autre !");
            Redirect::redirectToHome();
        }

        if (UserSettingsEntity::getInstance()->getProfilePageStatus() === 1) {
            Redirect::redirect('profile/', ['pseudo' => $user?->getPseudo()]);
        }

        $view = new View('Users', 'profile');
        $view->addVariableList(['user' => $user]);
        $view->view();
    }

    #[Link('/profile', Link::POST)]
    private function publicProfilePost(): void
    {
        if (!UserSettingsEntity::getInstance()->isProfilePageEnabled()) {
            Redirect::redirectToHome();
        }

        if (!UsersController::isUserLogged()) {
            Redirect::redirectToHome();
        }

        $image = $_FILES['pictureProfile'];

        try {
            // Upload image on the server
            $imageName = ImagesManager::upload($image, 'Users');
        } catch (ImagesException $e) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.errors.upload.image') . " => $e");
            Redirect::redirectPreviousRoute();
        }

        UserPictureModel::getInstance()->uploadImage(UsersModel::getCurrentUser()?->getId(), $imageName);

        Redirect::redirect('profile');
    }

    #[Link('/profile/:pseudo', Link::GET, ['pseudo' => '.*?'])]
    private function publicProfileWithPseudo(string $pseudo): void
    {
        if (!UsersController::isUserLogged() && !UserSettingsEntity::getInstance()->isProfilePageEnabled()) {
            Redirect::redirect('login');
        }

        if (!UserSettingsEntity::getInstance()->isProfilePageEnabled()) {
            Redirect::redirectToHome();
        }

        if (UserSettingsEntity::getInstance()->getProfilePageStatus() === 0) {
            Redirect::redirect('profile');
        }

        $user = UsersModel::getInstance()->getUserWithPseudo($pseudo);

        if (is_null($user)) {
            Redirect::errorPage(404);
        }

        if (UsersModel::getCurrentUser()?->getId() !== $user->getId()) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'), "Vous ne pouvez pas éditer le profile de quelqu'un d'autre !");
            Redirect::redirectToHome();
        }

        $view = new View('Users', 'profile');
        $view->addVariableList(['user' => $user]);
        $view->view();
    }

    #[NoReturn] #[Link('/profile/delete/:id', Link::GET, ['id' => '[0-9]+'])]
    private function publicProfileDelete(int $id): void
    {
        // Check if this is the current user account
        if (UsersModel::getCurrentUser()?->getId() !== $id) {
            // TODO ERROR MANAGEMENT (MESSAGE TO TELL THE USER CAN'T DELETE THIS ACCOUNT)
            Redirect::errorPage(403);
        }

        Emitter::send(DeleteUserAccountEvent::class, $id);

        UsersModel::logOut();
        UsersModel::getInstance()->delete($id);

        Redirect::redirectToHome();
    }

    #[NoReturn] #[Link('/profile/update', Link::POST)]
    private function publicProfileUpdate(): void
    {
        if (!UsersController::isUserLogged()) {
            Redirect::redirectToHome();
        }

        $user = UsersModel::getCurrentUser();

        [$mail, $pseudo, $firstname, $lastname] = Utils::filterInput('email', 'pseudo', 'name', 'lastname');

        if (UsersSettingsModel::getInstance()->isPseudoBlacklisted($pseudo)) {
            Flash::send(Alert::ERROR, LangManager::translate('users.toaster.error'),
                LangManager::translate('users.toaster.blacklisted_pseudo'));
            Redirect::redirectPreviousRoute();
        }

        $roles = UsersModel::getRoles($user?->getId());

        $rolesId = [];

        foreach ($roles as $role) {
            $rolesId[] = $role->getId();
        }

        $encryptedMail = EncryptManager::encrypt($mail);

        if (UsersModel::getInstance()->update($user?->getId(), $encryptedMail, $pseudo, $firstname, $lastname, $rolesId)) {
            Flash::send(Alert::SUCCESS, LangManager::translate('users.toaster.success'), LangManager::translate('users.toaster.user_edited_self'));
        } else {
            Flash::send(Alert::ERROR, LangManager::translate('users.toaster.error'), LangManager::translate('users.toaster.user_edited_self_nop'));
        }

        [$pass, $passVerif] = Utils::filterInput('password', 'passwordVerif');

        if (!is_null($pass)) {
            if ($pass === $passVerif) {
                UsersModel::getInstance()->updatePass($user?->getId(), password_hash($pass, PASSWORD_BCRYPT));
            } else {
                // Todo Try to edit that
                Flash::send(Alert::ERROR, LangManager::translate('users.toaster.error'), 'Je sais pas ?');
            }
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/profile/2fa/toggle', Link::POST)]
    private function publicProfile2FaToggle(): void
    {
        [$enforceMail] = Utils::filterInput('enforce_mail');

        if (!empty($enforceMail)) {
            $encryptedMail = EncryptManager::encrypt(mb_strtolower($enforceMail));
            $user = UsersModel::getInstance()->getUserWithMail($encryptedMail);
            $enforced = true;
        } else {
            $user = UsersModel::getCurrentUser();
            $enforced = false;
        }

        if (is_null($user)) {
            Flash::send(Alert::ERROR, LangManager::translate('users.toaster.error'),
                LangManager::translate('core.toaster.internalError'));
            Redirect::redirectToHome();
        }

        if (!isset($_POST['secret'])) {
            Flash::send(Alert::ERROR, LangManager::translate('users.toaster.error'),
                "Merci de remplir le code d'authentification");
            return;
        }

        $secret = FilterManager::filterInputIntPost('secret', 6);

        if (strlen($secret) !== 6) {
            Flash::send(Alert::ERROR, LangManager::translate('users.toaster.error'),
                'Code invalide.');
            Redirect::redirectPreviousRoute();
        }

        $tfa = new TwoFaManager();
        if (!$tfa->isSecretValid($user->get2Fa()->get2FaSecretDecoded(), $secret)) {
            Flash::send(Alert::ERROR, LangManager::translate('users.toaster.error'),
                'Code invalide.');
            Redirect::redirectPreviousRoute();
        }

        $status = $user->get2Fa()->isEnabled() ? 0 : 1;

        if ($user->get2Fa()->isEnforced() && $user->get2Fa()->isEnabled()) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'), 'Vous ne pouvez pas désactiver le double facteur sur ce compte !');
        } else {
            if (Users2FaModel::getInstance()->toggle2Fa($user->getId(), $status)) {
                UsersModel::updateStoredUser($user->getId());
                if ($enforced) {
                    Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
                        '2fa activée, veuillez vous reconnecter.');
                } else {
                    Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
                        $status ? '2fa activée' : '2fa désactivée');
                }
            } else {
                Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
                    LangManager::translate('core.toaster.internalError'));
            }
        }

        Redirect::redirectPreviousRoute();
    }

}
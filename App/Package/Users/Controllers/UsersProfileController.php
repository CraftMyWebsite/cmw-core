<?php

namespace CMW\Controller\Users;

use CMW\Entity\Users\UserSettingsEntity;
use CMW\Event\Users\DeleteUserAccountEvent;
use CMW\Event\Users\UpdateUserProfileEvent;
use CMW\Interface\Users\IUsersProfilePicture;
use CMW\Manager\Events\Emitter;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Loader\Loader;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Router\RouterException;
use CMW\Manager\Security\EncryptManager;
use CMW\Manager\Twofa\TwoFaManager;
use CMW\Manager\Views\View;
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

/**
 * Class: @UsersProfileController
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 0.0.1
 */
class UsersProfileController extends AbstractController
{
    /**
     * @throws RouterException
     */
    #[Link('/profile', Link::GET)]
    private function publicProfile(): void
    {
        $user = UsersSessionsController::getInstance()->getCurrentUser();
        $isProfilePageEnabled = UserSettingsEntity::getInstance()->isProfilePageEnabled();

        if (is_null($user) && !$isProfilePageEnabled) {
            Redirect::redirect('login');
        }

        if (!$isProfilePageEnabled) {
            Redirect::redirectToHome();
        }

        if (is_null($user)) {
            Redirect::redirect('login');
        }

        if (UsersSessionsController::getInstance()->getCurrentUser()?->getId() !== $user->getId()) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'), "Vous ne pouvez pas éditer le profile de quelqu'un d'autre !");
            Redirect::redirectToHome();
        }

        if (UserSettingsEntity::getInstance()->getProfilePageStatus() === 1) {
            Redirect::redirect('profile/', ['pseudo' => $user->getPseudo()]);
        }

        View::createPublicView('Users', 'profile')
            ->addVariableList(['user' => $user])
            ->view();
    }

    #[Link('/profile/update/picture', Link::POST)]
    private function publicProfilePost(): void
    {
        if (!UserSettingsEntity::getInstance()->isProfilePageEnabled()) {
            Redirect::redirectToHome();
        }

        $user = UsersSessionsController::getInstance()->getCurrentUser();

        if (is_null($user)) {
            Redirect::redirectToHome();
        }

        if (!isset($_FILES['pictureProfile']) || empty($_FILES['pictureProfile']['name'])) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                LangManager::translate('core.imageManager.error.emptyFile'),
            );
            Redirect::redirectPreviousRoute();
        }

        $image = $_FILES['pictureProfile'];
        Loader::getHighestImplementation(IUsersProfilePicture::class)->changeMethod($image, $user->getId());
    }

    #[Link('/profile/:pseudo', Link::GET, ['pseudo' => '.*?'])]
    private function publicProfileWithPseudo(string $pseudo): void
    {
        $user = UsersSessionsController::getInstance()->getCurrentUser();
        $isProfilePageEnabled = UserSettingsEntity::getInstance()->isProfilePageEnabled();

        if (is_null($user) && !$isProfilePageEnabled) {
            Redirect::redirect('login');
        }

        if (!$isProfilePageEnabled) {
            Redirect::redirectToHome();
        }

        if (UserSettingsEntity::getInstance()->getProfilePageStatus() === 0) {
            Redirect::redirect('profile');
        }

        if (is_null($user)) {
            Redirect::errorPage(404);
        }

        if (UsersSessionsController::getInstance()->getCurrentUser()?->getId() !== $user->getId()) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'), "Vous ne pouvez pas éditer le profile de quelqu'un d'autre !");
            Redirect::redirectToHome();
        }

        View::createPublicView('Users', 'profile')
            ->addVariableList(['user' => $user])
            ->view();
    }

    #[NoReturn] #[Link('/account/delete/:id', Link::GET, ['id' => '[0-9]+'])]
    private function publicProfileDelete(int $id): void
    {
        // Check if this is the current user account
        if (UsersSessionsController::getInstance()->getCurrentUser()?->getId() !== $id) {
            // TODO ERROR MANAGEMENT (MESSAGE TO TELL THE USER CAN'T DELETE THIS ACCOUNT)
            Redirect::errorPage(403);
        }

        Emitter::send(DeleteUserAccountEvent::class, $id);

        UsersSessionsController::getInstance()->logOut();
        UsersModel::getInstance()->delete($id);

        Redirect::redirectToHome();
    }

    #[NoReturn] #[Link('/profile/update', Link::POST)]
    private function publicProfileUpdate(): void
    {
        $user = UsersSessionsController::getInstance()->getCurrentUser();

        if (is_null($user)) {
            Redirect::redirectToHome();
        }

        $mail = FilterManager::filterInputStringPost('mail', 500);
        $pseudo = FilterManager::filterInputStringPost('pseudo');
        $firstname = FilterManager::filterInputStringPost('name', orElse: '');
        $lastname = FilterManager::filterInputStringPost('lastname', orElse: '');

        if (!FilterManager::isEmail($mail)) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('users.toaster.error'),
                LangManager::translate('users.toaster.invalid_mail'),
            );
            Redirect::redirectPreviousRoute();
        }

        if (UsersSettingsModel::getInstance()->isPseudoBlacklisted($pseudo)) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('users.toaster.error'),
                LangManager::translate('users.toaster.blacklisted_pseudo'),
            );
            Redirect::redirectPreviousRoute();
        }

        $roles = UsersModel::getRoles($user?->getId());

        $rolesId = [];

        foreach ($roles as $role) {
            $rolesId[] = $role->getId();
        }

        $encryptedMail = EncryptManager::encrypt($mail);

        if (UsersModel::getInstance()->update($user?->getId(), $encryptedMail, $pseudo, $firstname, $lastname, $rolesId)) {
            Flash::send(
                Alert::SUCCESS,
                LangManager::translate('core.toaster.success'),
                LangManager::translate('users.toaster.user_edited_self'),
            );
        } else {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('users.toaster.error'),
                LangManager::translate('users.toaster.user_edited_self_nop'),
            );
        }

        $password = FilterManager::filterInputStringPost('password', orElse: '');
        $passwordVerif = FilterManager::filterInputStringPost('passwordVerif', orElse: '');

        if (!empty($password)) {
            if ($password === $passwordVerif) {
                UsersModel::getInstance()->updatePass($user?->getId(), password_hash($password, PASSWORD_BCRYPT));
            } else {
                Flash::send(
                    Alert::ERROR,
                    LangManager::translate('users.toaster.error'),
                    LangManager::translate('users.toaster.password_not_same'),
                );
            }
        }

        UsersSessionsController::getInstance()->updateStoredUser($user->getId());

        Emitter::send(UpdateUserProfileEvent::class, $user->getId());

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
            $user = UsersSessionsController::getInstance()->getCurrentUser();
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

        $secret = $_POST['secret'];

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
                UsersSessionsController::getInstance()->updateStoredUser($user->getId());
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
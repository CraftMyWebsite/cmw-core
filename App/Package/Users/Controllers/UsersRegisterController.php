<?php

namespace CMW\Controller\Users;

use CMW\Controller\Core\SecurityController;
use CMW\Entity\Users\UserEntity;
use CMW\Event\Users\RegisterEvent;
use CMW\Manager\Events\Emitter;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Security\EncryptManager;
use CMW\Manager\Views\View;
use CMW\Model\Users\UsersModel;
use CMW\Model\Users\UsersSettingsModel;
use CMW\Type\Users\LoginStatus;
use CMW\Utils\Redirect;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use function error_log;
use function is_null;
use function mb_strtolower;
use function password_hash;
use const PASSWORD_BCRYPT;

/**
 * Class: @UsersRegisterController
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 0.0.1
 */
class UsersRegisterController extends AbstractController
{

    /**
     * @throws \CMW\Manager\Router\RouterException
     */
    #[Link('/register', Link::GET)]
    private function register(): void
    {
        if (UsersController::isUserLogged()) {
            Redirect::redirectToHome();
        }

        $oAuths = UsersOAuthController::getInstance()->getEnabledImplementations();

        View::createPublicView('Users', 'register')
            ->addVariableList(['oAuths' => $oAuths])
            ->view();
    }

    #[NoReturn] #[Link('/register', Link::POST)]
    private function registerPost(): void
    {
        //Prevent post exploits
        if (UsersController::isUserLogged()) {
            Redirect::redirectToHome();
        }

        //Check captcha
        if (!SecurityController::checkCaptcha()) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('users.toaster.error'),
                LangManager::translate('users.security.captcha.invalid'),
            );
            Redirect::redirectPreviousRoute();
        }

        //Filter data
        $mail = FilterManager::filterInputStringPost('register_email');
        $encryptedMail = EncryptManager::encrypt(mb_strtolower($mail));
        $pseudo = FilterManager::filterInputStringPost('register_pseudo');

        $password = FilterManager::filterInputStringPost('register_password');
        $passwordVerify = FilterManager::filterInputStringPost('register_password_verify');


        //Check if pseudo and mail are correct
        $this->basicChecks($pseudo, $mail, $encryptedMail);

        //Check if password matches
        $this->checkIfPasswordMatches($password, $passwordVerify);

        //Create user
        $user = UsersModel::getInstance()->create(
            $encryptedMail,
            $pseudo,
            null,
            null,
            RolesController::getInstance()->getDefaultRolesId(),
        );

        //Check if we return the user object
        if (is_null($user)) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                LangManager::translate('users.toaster.internalError'),
            );
            Redirect::redirectPreviousRoute();
        }

        //Define password
        $this->definePassword($user, $password);

        //Send RegisterEvent
        try {
            Emitter::send(RegisterEvent::class, $user->getId());
        } catch (Exception) {
            error_log('Error while sending RegisterEvent');
        }

        //Login user
        $loginCheck = UsersLoginController::getInstance()->checkLogin($encryptedMail, $password);

        //Check if login worked
        $this->checkIfLoginWorked($loginCheck, $user);
    }

    /**
     * @param mixed $pseudo
     * @param mixed $mail
     * @param string $encryptedMail
     * @return void
     */
    private function basicChecks(string $pseudo, string $mail, string $encryptedMail): void
    {
        //Check if pseudo is already used
        if (UsersModel::getInstance()->checkPseudo($pseudo) > 0) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('users.toaster.error'),
                LangManager::translate('users.toaster.used_pseudo'),
            );
            Redirect::redirect('register');
        }

        //Check if mail is correct and is already used
        if (!FilterManager::isEmail($mail) || UsersModel::getInstance()->checkEmail($encryptedMail) > 0) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('users.toaster.error'),
                LangManager::translate('users.toaster.used_mail'),
            );
            Redirect::redirect('register');
        }

        //Check if pseudo is blacklisted
        if (UsersSettingsModel::getInstance()->isPseudoBlacklisted($pseudo)) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('users.toaster.error'),
                LangManager::translate('users.toaster.blacklisted_pseudo'),
            );
            Redirect::redirect('register');
        }
    }

    /**
     * @param mixed $password
     * @param mixed $passwordVerify
     * @return void
     */
    public function checkIfPasswordMatches(mixed $password, mixed $passwordVerify): void
    {
        if ($password !== $passwordVerify) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('users.toaster.error'),
                LangManager::translate('users.toaster.not_same_pass')
            );
            Redirect::redirect('register');
        }
    }

    /**
     * @param \CMW\Type\Users\LoginStatus $loginCheck
     * @param \CMW\Entity\Users\UserEntity $user
     * @return void
     */
    #[NoReturn] public function checkIfLoginWorked(LoginStatus $loginCheck, UserEntity $user): void
    {
        if ($loginCheck->name === LoginStatus::OK->name) {
            UsersLoginController::getInstance()->loginUser($user, 1);

            Flash::send(
                Alert::SUCCESS,
                LangManager::translate('users.toaster.success'),
                LangManager::translate('users.toaster.welcome'),
            );
            Redirect::redirect('profile');
        } else {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'),
            );
            Redirect::redirectPreviousRoute();
        }
    }

    /**
     * @param \CMW\Entity\Users\UserEntity $user
     * @param mixed $password
     * @return void
     */
    public function definePassword(UserEntity $user, mixed $password): void
    {
        if (!UsersModel::getInstance()->updatePass($user->getId(), password_hash($password, PASSWORD_BCRYPT))) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                LangManager::translate('users.toaster.internalError'),
            );
            Redirect::redirectPreviousRoute();
        }
    }
}
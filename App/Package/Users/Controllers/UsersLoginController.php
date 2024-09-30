<?php

namespace CMW\Controller\Users;

use CMW\Controller\Core\SecurityController;
use CMW\Entity\Users\UserEntity;
use CMW\Event\Users\LoginEvent;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Error\ErrorManager;
use CMW\Manager\Events\Emitter;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Security\EncryptManager;
use CMW\Manager\Theme\ThemeManager;
use CMW\Manager\Twofa\TwoFaManager;
use CMW\Manager\Views\View;
use CMW\Model\Users\UsersModel;
use CMW\Type\Users\LoginStatus;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use Exception;
use function error_log;
use function file_exists;
use function is_int;
use function is_null;
use function mb_strtolower;
use function strlen;
use function time;

class UsersLoginController extends AbstractController
{
    /**
     * @param string $mail <b>(Encrypted)</b>
     * @param string $password
     * @return \CMW\Type\Users\LoginStatus
     * @desc Complete login user.
     */
    public function checkLogin(string $mail, string $password): LoginStatus
    {
        $credentialStatus = UsersModel::getInstance()->isCredentialsMatch($mail, $password);

        // If all is ok:
        if (!is_int($credentialStatus)) {
            return $credentialStatus;
        }

        $user = UsersModel::getInstance()->getUserById($credentialStatus);

        if ($user === null) {
            return LoginStatus::INTERNAL_ERROR;
        }

        if ($user->get2Fa()->isEnforced()) {
            return $user->get2Fa()->isEnabled() ? LoginStatus::OK_NEED_2FA : LoginStatus::OK_ENFORCE_2FA;
        }

        return $user->get2Fa()->isEnabled() ? LoginStatus::OK_NEED_2FA : LoginStatus::OK;
    }

    /**
     * @param \CMW\Entity\Users\UserEntity $user
     * @param bool $cookie
     * @return void
     */
    public function loginUser(UserEntity $user, bool $cookie): void
    {
        $_SESSION['cmwUser'] = $user;

        if ($cookie) {
            setcookie('cmw_cookies_user_id', $user->getId(), time() + 60 * 60 * 24 * 30, '/', true, true);
        }

        UsersModel::getInstance()->updateLoggedTime($user->getId());
        try {
            Emitter::send(LoginEvent::class, $user->getId());
        } catch (Exception) {
            error_log('Error while sending login event.');
        }
    }

    #[Link('/login', Link::POST)]
    private function loginPost(): void
    {
        if (!SecurityController::checkCaptcha()) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('users.toaster.error'),
                LangManager::translate('users.security.captcha.invalid'),
            );
            Redirect::redirectPreviousRoute();
        }

        [$mail, $password, $previousRoute] = Utils::filterInput('login_email', 'login_password', 'previousRoute');

        if (Utils::containsNullValue($mail, $password)) {
            Flash::send(Alert::ERROR, LangManager::translate('users.toaster.error'),
                LangManager::translate('core.toaster.db.missing_inputs'));
            Redirect::redirectPreviousRoute();
        }

        $encryptedMail = EncryptManager::encrypt(mb_strtolower($mail));

        $cookie = isset($_POST['login_keep_connect']) && $_POST['login_keep_connect'] ? 1 : 0;

        $loginStatus = $this->checkLogin($encryptedMail, $password);

        switch ($loginStatus) {
            case LoginStatus::NOT_FOUND:
                Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                    LangManager::translate('users.toaster.not_registered_account'));
                Redirect::redirectPreviousRoute();
            case LoginStatus::NOT_MATCH:
                Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                    LangManager::translate('users.toaster.mail_pass_matching'));
                Redirect::redirectPreviousRoute();
            case LoginStatus::INTERNAL_ERROR:
                Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                    LangManager::translate('core.toaster.internalError'));
                Redirect::redirectPreviousRoute();
            case LoginStatus::OK:
                $user = UsersModel::getInstance()->getUserWithMail($encryptedMail);
                if (is_null($user)) {
                    Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                        LangManager::translate('core.toaster.internalError'));
                    Redirect::redirectPreviousRoute();
                }
                $this->loginUser($user, $cookie);
                if ($previousRoute) {
                    Redirect::redirectPreviousRoute();
                }

                Redirect::redirect('profile');
                break;
            case LoginStatus::OK_NEED_2FA:
                $user = UsersModel::getInstance()->getUserWithMail($encryptedMail);
                if (is_null($user)) {
                    Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                        LangManager::translate('core.toaster.internalError'));
                    Redirect::redirectPreviousRoute();
                }

                $_SESSION['cmw_temp_user_id'] = $user->getId();
                $_SESSION['cmw_temp_use_cookies'] = $cookie;

                $this->showLogin2Fa();
                break;
            case LoginStatus::OK_ENFORCE_2FA:
                $user = UsersModel::getInstance()->getUserWithMail($encryptedMail);
                if (is_null($user)) {
                    Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                        LangManager::translate('core.toaster.internalError'));
                    Redirect::redirectPreviousRoute();
                }

                $_SESSION['cmw_temp_user_id'] = $user->getId();
                $_SESSION['cmw_temp_use_cookies'] = $cookie;

                $this->enforceLogin2Fa($user);
                break;
        }
    }

    /**
     * @throws \CMW\Manager\Router\RouterException
     */
    #[Link('/login', Link::GET)]
    private function loginGet(): void
    {
        if (UsersController::isUserLogged()) {
            Redirect::redirectToHome();
        }

        $oAuths = UsersOAuthController::getInstance()->getImplementations();

        $view = new View('Users', 'login');
        $view->addVariableList(['oAuths' => $oAuths]);
        $view->view();
    }

    private function showLogin2Fa(): void
    {
        $filePath = EnvManager::getInstance()->getValue('DIR')
            . 'Public/Themes/'
            . ThemeManager::getInstance()->getCurrentTheme()->name()
            . '/Views/Users/2fa.view.php';

        if (!file_exists($filePath)) {
            ErrorManager::showCustomErrorPage("File not found", "The file $filePath doesn't exist.");
        }

        $view = new View('Users', '2fa');
        $view->view();
    }

    private function enforceLogin2Fa(UserEntity $user): void
    {
        $view = new View('Users', 'enforce2fa');
        $view->addVariableList(['user' => $user]);
        $view->view();
    }

    #[Link('/login/validate/tfa', Link::POST)]
    private function loginCheck2Fa(): void
    {
        if (!isset($_POST['code'])) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                'Merci de mettre votre code.');
            $this->showLogin2Fa();
            return;
        }

        $code = FilterManager::filterInputIntPost('code', 6);

        if (strlen($code) !== 6) {
            Flash::send(Alert::ERROR, LangManager::translate('users.toaster.error'),
                'Code invalide.');
            $this->showLogin2Fa();
            return;
        }

        if (!isset($_SESSION['cmw_temp_user_id'])) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'));
            $this->showLogin2Fa();
            return;
        }

        $user = UsersModel::getInstance()->getUserById($_SESSION['cmw_temp_user_id']);

        if (is_null($user)) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'));
            $this->showLogin2Fa();
            return;
        }

        $tfa = new TwoFaManager();
        if (!$tfa->isSecretValid($user->get2Fa()->get2FaSecretDecoded(), $code)) {
            Flash::send(Alert::ERROR, LangManager::translate('users.toaster.error'),
                'Code invalide.');
            $this->showLogin2Fa();
            return;
        }

        $useCookies = isset($_SESSION['cmw_temp_use_cookies']) ? $_SESSION['cmw_temp_use_cookies'] : 0;

        $this->loginUser($user, $useCookies);

        // Clean temp sessions
        unset($_SESSION['cmw_temp_user_id'],
            $_SESSION['cmw_temp_use_cookies']);

        // Redirect
        Redirect::redirect('profile');
    }
}
<?php

namespace CMW\Controller\Users;

use CMW\Controller\Core\SecurityController;
use CMW\Entity\Users\UserEntity;
use CMW\Event\Users\LoginEvent;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Error\ErrorManager;
use CMW\Manager\Events\Emitter;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Mail\MailManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Security\EncryptManager;
use CMW\Manager\Theme\ThemeManager;
use CMW\Manager\Twofa\TwoFaManager;
use CMW\Manager\Views\View;
use CMW\Model\Core\CoreModel;
use CMW\Model\Core\MailModel;
use CMW\Model\Users\UsersModel;
use CMW\Model\Users\UsersSettingsModel;
use CMW\Type\Users\LoginStatus;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use CMW\Utils\Website;
use DateTime;
use Exception;
use function error_log;
use function file_exists;
use function is_int;
use function is_null;
use function mb_strtolower;
use function strlen;
use function time;

/**
 * Class: @UsersLoginController
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 0.0.1
 */
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

        $userLastConnect = $user->getLastConnectionUnformatted();

        if ((UsersSettingsModel::getSetting('securityReinforced') === '1') && $this->isUserInactiveFor90Days($userLastConnect) && !$user->get2Fa()->isEnabled() && MailModel::getInstance()->getConfig() !== null && MailModel::getInstance()->getConfig()->isEnable()) {
            return LoginStatus::OK_LONG_DATE;
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
            case LoginStatus::OK_LONG_DATE:
                $user = UsersModel::getInstance()->getUserWithMail($encryptedMail);
                if (is_null($user)) {
                    Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                        LangManager::translate('core.toaster.internalError'));
                    Redirect::redirectPreviousRoute();
                }

                $_SESSION['cmw_temp_user_id'] = $user->getId();
                $_SESSION['cmw_temp_use_cookies'] = $cookie;

                $code = Utils::generateRandomNumber(6);
                $encryptedCode = EncryptManager::encrypt($code);
                $encryptedMail = EncryptManager::encrypt($mail);
                $this->sendLongDateCodeByMail($user->getMail(), $code);
                $userModel = UsersModel::getInstance();
                $userModel->deleteLongDateCode($encryptedMail);

                if ($userModel->addLongDateCode($encryptedMail, $encryptedCode)) {
                    Flash::send(Alert::SUCCESS, LangManager::translate('users.long_date.toaster.title'), LangManager::translate('users.long_date.toaster.receive_by_mail'));
                    $this->needMailCodeCheck();
                    break;
                }

                Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'), LangManager::translate('users.long_date.toaster.unable_to_create_code'));
                Redirect::redirectToHome();
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

        $oAuths = UsersOAuthController::getInstance()->getEnabledImplementations();

        View::createPublicView('Users', 'login')
            ->addVariableList(['oAuths' => $oAuths])
            ->view();
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

        View::createPublicView('Users', '2fa')->view();
    }

    private function enforceLogin2Fa(UserEntity $user): void
    {
        View::createPublicView('Users', 'enforce2fa')
            ->addVariableList(['user' => $user])
            ->view();
    }

    #[Link('/login/validate/tfa', Link::POST)]
    private function loginCheck2Fa(): void
    {
        if (!isset($_POST['code'])) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('users.long_date.toaster.put_the_code'));
            $this->showLogin2Fa();
            return;
        }

        $code = $_POST['code'];

        if (strlen($code) !== 6) {
            Flash::send(Alert::ERROR, LangManager::translate('users.toaster.error'),
                LangManager::translate('users.long_date.toaster.invalid_code'));
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

    private function needMailCodeCheck(): void
    {
        View::createPublicView('Users', 'longDateCheck')
            ->view();
    }

    #[Link('/login/validate/longDate', Link::POST)]
    private function loginCheckLongDate(): void
    {
        if (!isset($_POST['code'])) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('users.long_date.toaster.put_the_code'));
            $this->needMailCodeCheck();
            return;
        }

        $code = $_POST['code'];

        if (strlen($code) !== 6) {
            Flash::send(Alert::ERROR, LangManager::translate('users.toaster.error'),
                LangManager::translate('users.long_date.toaster.invalid_code'));
            $this->needMailCodeCheck();
            return;
        }

        if (!isset($_SESSION['cmw_temp_user_id'])) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'));
            $this->needMailCodeCheck();
            return;
        }

        $user = UsersModel::getInstance()->getUserById($_SESSION['cmw_temp_user_id']);

        if (is_null($user)) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'));
            $this->needMailCodeCheck();
            return;
        }

        $userModel = UsersModel::getInstance();
        $encryptedCode = EncryptManager::encrypt($code);
        $encryptedMail = EncryptManager::encrypt($user->getMail());
        $dbCode = $userModel->getCodeByCodeAndUserMail($encryptedMail, $encryptedCode);
        if (!is_null($dbCode)) {
            if ($this->isCodeOlderThan15Minutes($encryptedMail)) {
                Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'), LangManager::translate('users.long_date.toaster.too_late'));
                $userModel->deleteLongDateCode($encryptedMail);
                Redirect::redirect('login');
            }
            $decryptedDbCode = EncryptManager::decrypt($dbCode);
            if ($code !== $decryptedDbCode) {
                Flash::send(Alert::ERROR, LangManager::translate('users.toaster.error'),
                    LangManager::translate('users.long_date.toaster.invalid_code'));
                $this->needMailCodeCheck();
                return;
            }
        } else {
            Flash::send(Alert::ERROR, LangManager::translate('users.toaster.error'),
                LangManager::translate('users.long_date.toaster.invalid_code'));
            $this->needMailCodeCheck();
            return;
        }

        $userModel->deleteLongDateCode($encryptedMail);

        $useCookies = isset($_SESSION['cmw_temp_use_cookies']) ? $_SESSION['cmw_temp_use_cookies'] : 0;

        $this->loginUser($user, $useCookies);

        // Clean temp sessions
        unset($_SESSION['cmw_temp_user_id'],
            $_SESSION['cmw_temp_use_cookies']);

        // Redirect
        Redirect::redirect('profile');
    }

    /**
     * @param string $email
     * @param string $code
     * @return void
     */
    public function sendLongDateCodeByMail(string $email, string $code): void
    {
        $body = '
        <b>'. LangManager::translate('users.long_date.mail.body_1') . Website::getWebsiteName() .'</b><br>
        <p>'. LangManager::translate('users.long_date.mail.body_2') .'</p>
        <h2 style="text-align: center">'.  $code  .'</h2>
        <p>'. LangManager::translate('users.long_date.mail.body_3') .'</p>
        ';

        MailManager::getInstance()->sendMail($email, LangManager::translate('users.login.forgot_password.mail.object_link',
            ['site_name' => (new CoreModel())->fetchOption('name')]),$body);
    }

    public function isCodeOlderThan15Minutes(string $email): bool
    {
        $linkDate = UsersModel::getInstance()->getLongDateCodeDate($email);

        if (is_null($linkDate)) {
            return false;
        }

        $linkTimestamp = strtotime($linkDate);

        $timeDifference = time() - $linkTimestamp;


        return $timeDifference > 900;
    }

    public function isUserInactiveFor90Days($userLastConnect): bool
    {
        $lastConnectionDate = new DateTime($userLastConnect);

        $currentDate = new DateTime();

        $interval = $currentDate->diff($lastConnectionDate);

        return $interval->days >= 90;
    }
}
<?php

namespace CMW\Controller\Users;

use CMW\Controller\Core\SecurityController;
use CMW\Entity\Users\UserPictureEntity;
use CMW\Event\Users\DeleteUserAccountEvent;
use CMW\Event\Users\LogoutEvent;
use CMW\Event\Users\RegisterEvent;
use CMW\Interface\Users\IUsersProfilePicture;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Events\Emitter;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Loader\Loader;
use CMW\Manager\Mail\MailManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Security\EncryptManager;
use CMW\Manager\Views\View;
use CMW\Model\Core\CoreModel;
use CMW\Model\Users\RolesModel;
use CMW\Model\Users\UsersModel;
use CMW\Model\Users\UsersSettingsModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use CMW\Utils\Website;
use Exception;
use http\Client\Curl\User;
use JetBrains\PhpStorm\NoReturn;
use JsonException;

/**
 * Class: @UsersController
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 0.0.1
 */
class UsersController extends AbstractController
{
    public static function isAdminLogged(): bool
    {
        return UsersModel::hasPermission(UsersSessionsController::getInstance()->getCurrentUser(), 'core.dashboard');
    }

    /**
     * @return bool
     * @desc Return true if the current user / client is logged.
     */
    public static function isUserLogged(): bool
    {
        return UsersSessionsController::getInstance()->getCurrentUser() !== null;
    }

    public static function hasPermission(string ...$permissions): bool
    {
        return UsersModel::hasPermission(UsersSessionsController::getInstance()->getCurrentUser(), ...$permissions);
    }

    /**
     * @param int|null $userId
     * @return \CMW\Entity\Users\UserPictureEntity|null
     */
    public function getUserProfilePicture(?int $userId = null): ?UserPictureEntity
    {
        if ($userId === null) {
            $user = UsersSessionsController::getInstance()->getCurrentUser();

            if ($user === null) {
                return null;
            }

            $userId = $user->getId();
        }
        return Loader::getHighestImplementation(IUsersProfilePicture::class)->getUserProfilePicture($userId);
    }

    #[Link(path: '/', method: Link::GET, scope: '/cmw-admin/users')]
    #[Link('/manage', Link::GET, [], '/cmw-admin/users')]
    private function adminUsersList(): void
    {
        self::redirectIfNotHavePermissions('core.dashboard', 'users.manage');

        $userList = UsersModel::getInstance()->getUsers();
        $roles = RolesModel::getInstance()->getRoles();

        View::createAdminView('Users', 'manage')
            ->addVariableList(['userList' => $userList, 'roles' => $roles])
            ->addStyle('Admin/Resources/Assets/Css/simple-datatables.css')
            ->addScriptBefore('App/Package/Users/Views/Assets/Js/edit.js')
            ->addScriptAfter('Admin/Resources/Vendors/Simple-datatables/simple-datatables.js',
                'Admin/Resources/Vendors/Simple-datatables/config-datatables.js')
            ->view();
    }

    public static function redirectIfNotHavePermissions(string ...$permCode): void
    {
        if (!(self::hasPermission(...$permCode))) {
            Redirect::redirectToHome();
        }
    }

    #[Link('/getUser/:id', Link::GET, ['id' => '[0-9]+'], '/cmw-admin/users')]
    private function adminGetUser(int $id): void
    {
        self::redirectIfNotHavePermissions('core.dashboard', 'users.manage.edit');

        $user = (UsersModel::getInstance())->getUserById($id);

        $roles = [];

        foreach ($user?->getRoles() as $role) {
            $roles[] .= $role->getName();
        }

        $data = [
            'id' => $user?->getId(),
            'mail' => $user?->getMail(),
            'username' => $user?->getPseudo(),
            'firstName' => $user?->getFirstName() ?? '',
            'lastName' => $user?->getLastName() ?? '',
            'state' => $user?->getState(),
            'lastConnection' => $user?->getLastConnection(),
            'dateCreated' => $user?->getCreated(),
            'dateUpdated' => $user?->getUpdated(),
            'pictureLink' => $user?->getUserPicture()?->getImage(),
            'pictureLastUpdate' => $user?->getUserPicture()?->getLastUpdate(),
            'userHighestRole' => $user?->getHighestRole()?->getName(),
            'roles' => $roles,
        ];

        try {
            print_r(json_encode($data, JSON_THROW_ON_ERROR));
        } catch (JsonException) {
            print ('ERROR');
        }
    }

    #[Link('/edit/:id', Link::GET, ['id' => '[0-9]+'], '/cmw-admin/users/manage')]
    private function adminUsersEdit(int $id): void
    {
        self::redirectIfNotHavePermissions('core.dashboard', 'users.manage.edit');

        $userEntity = UsersModel::getInstance()->getUserById($id);

        $roles = RolesModel::getInstance()->getRoles();

        View::createAdminView('Users', 'user')
            ->addVariableList([
                'user' => $userEntity,
                'roles' => $roles,
            ])
            ->view();
    }

    #[NoReturn] #[Link('/edit/:id', Link::POST, ['id' => '[0-9]+'], '/cmw-admin/users/manage')]
    private function adminUsersEditPost(int $id): void
    {
        self::redirectIfNotHavePermissions('core.dashboard', 'users.manage.edit');

        [$pass, $passVerif] = Utils::filterInput('pass', 'passVerif');
        [$mail, $username, $firstname, $lastname] = Utils::filterInput('email', 'pseudo', 'name', 'lastname');

        $encryptedMail = EncryptManager::encrypt($mail);

        if (!isset($_POST['pass']) || $pass === '') {
            UsersModel::getInstance()->update($id, $encryptedMail, $username, $firstname, $lastname, $_POST['roles']);
            Flash::send(Alert::SUCCESS, LangManager::translate('users.toaster.success'),
                LangManager::translate('users.toaster.edited_not_pass_change'));
        } else if ($pass === $passVerif) {
            UsersModel::getInstance()->updatePass($id, password_hash($pass, PASSWORD_BCRYPT));
            UsersModel::getInstance()->update($id, $encryptedMail, $username, $firstname, $lastname, $_POST['roles']);
            Flash::send(Alert::SUCCESS, LangManager::translate('users.toaster.success'),
                LangManager::translate('users.toaster.edited_pass_change'));
        } else {
            Flash::send(Alert::ERROR, LangManager::translate('users.toaster.error'),
                LangManager::translate('users.toaster.not_same_pass'));
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/add', Link::POST, [], '/cmw-admin/users')]
    private function adminUsersAddPost(): void
    {
        self::redirectIfNotHavePermissions('core.dashboard', 'users.manage.add');

        [$mail, $pseudo, $firstname, $lastname] = Utils::filterInput('email', 'pseudo', 'firstname', 'surname');

        $encryptedMail = EncryptManager::encrypt(mb_strtolower($mail));

        $userEntity = UsersModel::getInstance()->create($encryptedMail, $pseudo, $firstname, $lastname, $_POST['roles']);

        if ($userEntity === null) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('users.toaster.error_add'));
            Redirect::redirectPreviousRoute();
        }

        UsersModel::getInstance()->updatePass($userEntity?->getId(), password_hash(filter_input(INPUT_POST, 'password'), PASSWORD_BCRYPT));

        $userId = $userEntity->getId();

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('users.toaster.success_add', ['pseudo' => $pseudo]));

        Emitter::send(RegisterEvent::class, $userId);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/manage/state/:id/:state', Link::GET, ['id' => '[0-9]+', 'state' => '[0-9]+'], '/cmw-admin/users')]
    private function adminUserState(int $id, int $state): void
    {
        self::redirectIfNotHavePermissions('core.dashboard', 'users.manage.edit');

        if (UsersSessionsController::getInstance()->getCurrentUser()?->getId() === $id) {
            Flash::send(Alert::ERROR, LangManager::translate('users.toaster.error'),
                LangManager::translate('users.toaster.impossible'));
            Redirect::redirectPreviousRoute();
        }

        $state = ($state) ? 0 : 1;

        UsersModel::getInstance()->changeState($id, $state);

        Flash::send(Alert::SUCCESS, LangManager::translate('users.toaster.success'),
            LangManager::translate('users.toaster.status'));

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/delete/:id', Link::GET, ['id' => '[0-9]+'], '/cmw-admin/users')]
    private function adminUsersDelete(int $id): void
    {
        self::redirectIfNotHavePermissions('core.dashboard', 'users.manage.delete');

        if (UsersSessionsController::getInstance()->getCurrentUser()?->getId() === $id) {
            // Todo Try to remove that
            Flash::send(Alert::ERROR, LangManager::translate('users.toaster.error'),
                LangManager::translate('users.toaster.impossible_user'));
            Redirect::redirectPreviousRoute();
        }

        Emitter::send(DeleteUserAccountEvent::class, $id);

        UsersModel::getInstance()->delete($id);

        // Todo Try to remove that
        Flash::send(Alert::SUCCESS, LangManager::translate('users.toaster.success'),
            LangManager::translate('users.toaster.user_deleted'));

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/picture/edit/:id', Link::POST, ['id' => '[0-9]+'], '/cmw-admin/users/manage')]
    private function adminUsersEditPicturePost(int $id): void
    {
        self::redirectIfNotHavePermissions('core.dashboard', 'users.manage.edit');

        $image = $_FILES['profilePicture'];
        Loader::getHighestImplementation(IUsersProfilePicture::class)->changeMethod($image, $id);
    }

    #[Link('/picture/reset/:id', Link::GET, ['id' => '[0-9]+'], '/cmw-admin/users/manage')]
    #[NoReturn]
    private function adminUsersResetPicture(int $id): void
    {
        self::redirectIfNotHavePermissions('core.dashboard', 'users.edit');

        Loader::getHighestImplementation(IUsersProfilePicture::class)->resetPicture($id);
    }

    // PUBLIC SECTION

    /**
     * @throws \CMW\Manager\Router\RouterException
     */
    #[Link('/login/forgot', Link::GET)]
    private function forgotPassword(): void
    {
        if (self::isUserLogged()) {
            Redirect::redirectToHome();
        }

        View::createPublicView('Users', 'forgot_password')->view();
    }

    #[NoReturn] #[Link('/login/forgot', Link::POST)]
    private function forgotPasswordPost(): void
    {
        if (SecurityController::checkCaptcha()) {
            $mail = filter_input(INPUT_POST, 'mail');

            $encryptedMail = EncryptManager::encrypt($mail);

            // We check if this email exist
            if (UsersModel::getInstance()->checkEmail($encryptedMail) <= 0) {
                Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                    LangManager::translate('users.toaster.not_registered_account'));

                Redirect::redirectPreviousRoute();
            }
            // We send a verification link for this mail
            if (UsersSettingsModel::getSetting('resetPasswordMethod') === '0') {
                $this->resetPasswordMethodPasswordSendByMail($encryptedMail);

                Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
                    LangManager::translate('users.toaster.password_reset', ['mail' => $mail]));

            } elseif (UsersSettingsModel::getSetting('resetPasswordMethod') === '1') {
                $this->resetPasswordMethodUniqueLinkSendByMail($encryptedMail);

                Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'), LangManager::translate('users.toaster.reset_link_follow_the_link'));
            }

            if (str_starts_with($_SERVER['HTTP_REFERER'], EnvManager::getInstance()->getValue('PATH_URL') . 'cmw-admin/')) {
                Redirect::redirectPreviousRoute();
            }

            Redirect::redirect('login');
        } else {
            Flash::send(Alert::WARNING, 'Captcha', LangManager::translate('users.security.captcha.invalid'));
            Redirect::redirectPreviousRoute();
        }
    }

    /**
     * @desc database contain encrypted, and user have the decrypted so the secret in db can't pass this verification
     */
    #[NoReturn] #[Link('/resetPassword/:secret', Link::GET)]
    private function resetPasswordSecret(string $secret): void
    {
        $encryptedLink = EncryptManager::encrypt($secret);
        $dbSecret = UsersModel::getInstance()->getSecretLink($encryptedLink);

        if (is_null($dbSecret)) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'), LangManager::translate('users.toaster.reset_link_not_found'));
            Redirect::redirectToHome();
        } else {
            $userMail = UsersModel::getInstance()->getMailBySecretLink($encryptedLink);
            if ($this->isLinkOlderThan15Minutes($userMail)) {
                UsersModel::getInstance()->deleteSecretLink($userMail);
                Flash::send(Alert::WARNING, LangManager::translate('core.toaster.error'), LangManager::translate('users.toaster.reset_link_not_available'));
                Redirect::redirect('login');
            } else {
                View::createPublicView('Users', 'newPassword')->view();
            }
        }
    }

    #[NoReturn] #[Link('/resetPassword/:secret', Link::POST)]
    private function resetPasswordSecretPost(string $secret): void
    {
        if (UsersSessionsController::getInstance()->getCurrentUser()) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'), LangManager::translate('users.toaster.reset_link_log_out'));
            Redirect::redirect('login');
        }
        if (SecurityController::checkCaptcha()) {
            $encryptedLink = EncryptManager::encrypt($secret);
            $encryptedMail = UsersModel::getInstance()->getMailBySecretLink($encryptedLink);

            $password = FilterManager::filterInputStringPost('reset_password');
            $passwordVerify = FilterManager::filterInputStringPost('reset_password_verify');

            UsersRegisterController::getInstance()->checkIfPasswordMatches($password, $passwordVerify);

            UsersModel::getInstance()->updatePassWithMail($encryptedMail, password_hash($password, PASSWORD_BCRYPT));

            UsersModel::getInstance()->deleteSecretLink($encryptedMail);

            Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'), LangManager::translate('users.toaster.reset_link_pass_changed'));

            Redirect::redirect('login');
        } else {
            // TODO Toaster invalid captcha
            Redirect::redirectPreviousRoute();
        }
    }

    #[NoReturn] #[Link('/logout', Link::GET)]
    private function logOut(): void
    {
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();
        Emitter::send(LogoutEvent::class, $userId);
        UsersSessionsController::getInstance()->logOut();
        Redirect::redirectToHome();
    }

    /*---------------------------------
     *      PASSWORD RESET METHOD
     *--------------------------------- */

    /**
     * @param string $email
     * @return void
     */
    public function resetPasswordMethodUniqueLinkSendByMail(string $email): void
    {
        $linkToken = Utils::genId(100);

        $userModel = UsersModel::getInstance();

        $encryptedLink = EncryptManager::encrypt($linkToken);
        if ($userMail = $userModel->secretExistByMail($email)) {
            if ($this->isLinkOlderThan15Minutes($userMail)) {
                $userModel->deleteSecretLink($email);
            } else {
                Flash::send(Alert::WARNING, LangManager::translate('core.toaster.error'), LangManager::translate('users.toaster.reset_in_progress'));
                Redirect::redirect('login');
            }
        }

        $userModel->addSecretLink($email, $encryptedLink);

        $this->sendResetLinkPassword($email, $linkToken);
    }

    public function isLinkOlderThan15Minutes(string $email): bool
    {
        $linkDate = UsersModel::getInstance()->getSecretLinkDate($email);

        if (is_null($linkDate)) {
            return false;
        }

        $linkTimestamp = strtotime($linkDate);

        $timeDifference = time() - $linkTimestamp;


        return $timeDifference > 900;
    }

    /**
     * @param string $email
     * @param string $link
     * @return void
     */
    public function sendResetLinkPassword(string $email, string $link): void
    {
        $decryptedMail = EncryptManager::decrypt($email);
        $fullLink = EnvManager::getInstance()->getValue('PATH_URL') . 'resetPassword/'.$link;

        $body = '
        <b>'. LangManager::translate('users.toaster.reset_link_body_mail_1') . Website::getWebsiteName() .'</b><br>
        <p>'. LangManager::translate('users.toaster.reset_link_body_mail_2') .'</p>
        <p>'. LangManager::translate('users.toaster.reset_link_body_mail_3') .'</p>
        <a href="'. $fullLink .'">'. LangManager::translate('users.toaster.reset_link_body_mail_4') .'</a>
        <br><br>
        <p>'. LangManager::translate('users.toaster.reset_link_body_mail_5') .'</p>
        ';

        MailManager::getInstance()->sendMail($decryptedMail, LangManager::translate('users.login.forgot_password.mail.object_link',
            ['site_name' => (new CoreModel())->fetchOption('name')]),$body);
    }

    /**
     * @param string $email
     * @return void
     */
    public function resetPasswordMethodPasswordSendByMail(string $email): void
    {
        $newPassword = $this->generatePassword();

        UsersModel::getInstance()->updatePassWithMail($email, password_hash($newPassword, PASSWORD_BCRYPT));

        $this->sendResetPassword($email, $newPassword);
    }

    /**
     * @param string $email
     * @param string $password
     * @return void
     */
    public function sendResetPassword(string $email, string $password): void
    {
        $decryptedMail = EncryptManager::decrypt($email);
        MailManager::getInstance()->sendMail($decryptedMail, LangManager::translate('users.login.forgot_password.mail.object_pass',
            ['site_name' => (new CoreModel())->fetchOption('name')]),
            LangManager::translate('users.login.forgot_password.mail.body',
                ['password' => $password]));
    }

    /**
     * @return string
     * @desc Generate random password
     */
    private function generatePassword(): string
    {
        try {
            return bin2hex(Utils::genId(random_int(7, 12)));
        } catch (Exception) {
            return bin2hex(Utils::genId(10));
        }
    }
}

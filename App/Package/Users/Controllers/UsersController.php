<?php

namespace CMW\Controller\Users;

use CMW\Controller\Core\SecurityController;
use CMW\Entity\Users\UserEntity;
use CMW\Entity\Users\UserPictureEntity;
use CMW\Entity\Users\UserSettingsEntity;
use CMW\Event\Users\DeleteUserAccountEvent;
use CMW\Event\Users\LoginEvent;
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
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Security\EncryptManager;
use CMW\Manager\Twofa\TwoFaManager;
use CMW\Manager\Views\View;
use CMW\Model\Users\RolesModel;
use CMW\Model\Users\UserPictureModel;
use CMW\Model\Users\Users2FaModel;
use CMW\Model\Users\UsersModel;
use CMW\Model\Users\UsersSettingsModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use JsonException;

/**
 * Class: @UsersController
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class UsersController extends AbstractController
{
    public static function isAdminLogged(): bool
    {
        return UsersModel::hasPermission(UsersModel::getCurrentUser(), "core.dashboard");
    }

    /**
     * @param string $interface
     * @return mixed
     */
    private function getHighestImplementation(string $interface): mixed
    {
        $implementations = Loader::loadImplementations($interface);

        $index = 0;
        $highestWeight = 1;

        $i = 0;
        foreach ($implementations as $implementation) {
            $weight = $implementation->weight();

            if ($weight > $highestWeight) {
                $index = $i;
                $highestWeight = $weight;
            }
            ++$i;
        }

        return $implementations[$index];
    }

    /**
     * @return bool
     * @desc Return true if the current user / client is logged.
     */
    public static function isUserLogged(): bool
    {
        return UsersModel::getCurrentUser() !== null;
    }

    public static function hasPermission(string ...$permissions): bool
    {
        return UsersModel::hasPermission(UsersModel::getCurrentUser(), ...$permissions);
    }

    /**
     * @param string $mail <b>(Encrypted)</b>
     * @param string $password
     * @return \CMW\Controller\Users\LoginStatus
     * @desc Complete login user.
     */
    public function checkLogin(string $mail, string $password): LoginStatus
    {
        $credentialStatus = UsersModel::getInstance()->isCredentialsMatch($mail, $password);

        //If all is ok:
        if (!is_int($credentialStatus)) {
            return $credentialStatus;
        }

        $user = UsersModel::getInstance()->getUserById($credentialStatus);

        if ($user === null) {
            return LoginStatus::INTERNAL_ERROR;
        }

        return $user->get2Fa()->isEnabled() ? LoginStatus::OK_NEED_2FA : LoginStatus::OK;
    }

    /**
     * @param \CMW\Entity\Users\UserEntity $user
     * @param bool $cookie
     * @return void
     * @throws \ReflectionException
     */
    public function loginUser(UserEntity $user, bool $cookie): void
    {
        $_SESSION['cmwUser'] = $user;

        if ($cookie) {
            setcookie('cmw_cookies_user_id', $user->getId(), time() + 60 * 60 * 24 * 30, "/", true, true);
        }

        UsersModel::getInstance()->updateLoggedTime($user->getId());
        Emitter::send(LoginEvent::class, $user->getId());
    }

    /**
     * @param int|null $userId
     * @return \CMW\Entity\Users\UserPictureEntity|null
     */
    public function getUserProfilePicture(?int $userId = null): ?UserPictureEntity
    {
        if ($userId === null) {
            $user = UsersModel::getCurrentUser();

            if ($user === null) {
                return null;
            }

            $userId = $user->getId();
        }
        return $this->getHighestImplementation(IUsersProfilePicture::class)->getUserProfilePicture($userId);
    }

    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin/users")]
    #[Link("/manage", Link::GET, [], "/cmw-admin/users")]
    private function adminUsersList(): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.manage");

        $userList = UsersModel::getInstance()->getUsers();
        $roles = RolesModel::getInstance()->getRoles();


        View::createAdminView("Users", "manage")
            ->addVariableList(["userList" => $userList, "roles" => $roles])
            ->addStyle("Admin/Resources/Assets/Css/simple-datatables.css")
            ->addScriptBefore("App/Package/Users/Views/Assets/Js/edit.js")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/Umd/simple-datatables.js",
                "Admin/Resources/Vendors/Simple-datatables/config-datatables.js")
            ->view();
    }

    public static function redirectIfNotHavePermissions(string ...$permCode): void
    {
        if (!(self::hasPermission(...$permCode))) {
            Redirect::redirectToHome();
        }
    }

    #[Link("/getUser/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/users")]
    private function adminGetUser(Request $request, int $id): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.manage.edit");

        $user = (UsersModel::getInstance())->getUserById($id);

        $roles = [];

        foreach ($user?->getRoles() as $role) {
            $roles[] .= $role->getName();
        }

        $data = [
            "id" => $user?->getId(),
            "mail" => $user?->getMail(),
            "username" => $user?->getPseudo(),
            "firstName" => $user?->getFirstName() ?? "",
            "lastName" => $user?->getLastName() ?? "",
            "state" => $user?->getState(),
            "lastConnection" => $user?->getLastConnection(),
            "dateCreated" => $user?->getCreated(),
            "dateUpdated" => $user?->getUpdated(),
            "pictureLink" => $user?->getUserPicture()?->getImage(),
            "pictureLastUpdate" => $user?->getUserPicture()?->getLastUpdate(),
            "userHighestRole" => $user?->getHighestRole()?->getName(),
            "roles" => $roles,
        ];

        try {
            print_r(json_encode($data, JSON_THROW_ON_ERROR));
        } catch (JsonException) {
            print("ERROR");
        }
    }


    #[Link("/edit/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/users/manage")]
    private function adminUsersEdit(Request $request, int $id): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.manage.edit");

        $userEntity = UsersModel::getInstance()->getUserById($id);

        $roles = RolesModel::getInstance()->getRoles();

        View::createAdminView("Users", "user")->addVariableList([
            "user" => $userEntity,
            "roles" => $roles,
        ])
            ->view();
    }

    #[Link("/edit/:id", Link::POST, ["id" => "[0-9]+"], "/cmw-admin/users/manage")]
    #[NoReturn] private function adminUsersEditPost(Request $request, int $id): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.manage.edit");

        [$pass, $passVerif] = Utils::filterInput("pass", "passVerif");
        [$mail, $username, $firstname, $lastname] = Utils::filterInput("email", "pseudo", "name", "lastname");

        $encryptedMail = EncryptManager::encrypt($mail);

        if ($pass === "") {
            UsersModel::getInstance()->update($id, $encryptedMail, $username, $firstname, $lastname, $_POST['roles']);
            Flash::send(Alert::SUCCESS, LangManager::translate("users.toaster.success"),
                LangManager::translate("users.toaster.edited_not_pass_change"));
        } else if ($pass === $passVerif) {
            UsersModel::getInstance()->updatePass($id, password_hash($pass, PASSWORD_BCRYPT));
            UsersModel::getInstance()->update($id, $encryptedMail, $username, $firstname, $lastname, $_POST['roles']);
            Flash::send(Alert::SUCCESS, LangManager::translate("users.toaster.success"),
                LangManager::translate("users.toaster.edited_pass_change"));
        } else {
            Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),
                LangManager::translate("users.toaster.not_same_pass"));
        }

        Redirect::redirectPreviousRoute();
    }


    #[NoReturn] #[Link("/add", Link::POST, [], "/cmw-admin/users")]
    private function adminUsersAddPost(): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.manage.add");

        [$mail, $pseudo, $firstname, $lastname] = Utils::filterInput("email", "pseudo", "firstname", "surname");

        $encryptedMail = EncryptManager::encrypt(mb_strtolower($mail));

        $userEntity = UsersModel::getInstance()->create($encryptedMail, $pseudo, $firstname, $lastname, $_POST['roles']);

        if ($userEntity === null) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('users.toaster.error_add'));
            Redirect::redirectPreviousRoute();
        }

        UsersModel::getInstance()->updatePass($userEntity?->getId(), password_hash(filter_input(INPUT_POST, "password"), PASSWORD_BCRYPT));

        $userId = $userEntity->getId();

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('users.toaster.success_add', ['pseudo' => $pseudo]));

        Emitter::send(RegisterEvent::class, $userId);

        Redirect::redirectPreviousRoute();
    }

    #[Link("/manage/state/:id/:state", Link::GET, ["id" => "[0-9]+", "state" => "[0-9]+"], "/cmw-admin/users")]
    #[NoReturn] private function adminUserState(Request $request, int $id, int $state): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.manage.edit");

        if (UsersModel::getCurrentUser()?->getId() === $id) {
            Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),
                LangManager::translate("users.toaster.impossible"));
            Redirect::redirectPreviousRoute();
        }

        $state = ($state) ? 0 : 1;

        UsersModel::getInstance()->changeState($id, $state);

        Flash::send(Alert::SUCCESS, LangManager::translate("users.toaster.success"),
            LangManager::translate("users.toaster.status"));

        Redirect::redirectPreviousRoute();
    }

    #[Link("/delete/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/users")]
    #[NoReturn] private function adminUsersDelete(Request $request, int $id): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.manage.delete");

        if (UsersModel::getCurrentUser()?->getId() === $id) {

            //Todo Try to remove that
            Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),
                LangManager::translate("users.toaster.impossible_user"));
            Redirect::redirectPreviousRoute();
        }

        Emitter::send(DeleteUserAccountEvent::class, $id);

        UsersModel::getInstance()->delete($id);


        //Todo Try to remove that
        Flash::send(Alert::SUCCESS, LangManager::translate("users.toaster.success"),
            LangManager::translate("users.toaster.user_deleted"));

        Redirect::redirectPreviousRoute();
    }

    #[Link("/picture/edit/:id", Link::POST, ["id" => "[0-9]+"], "/cmw-admin/users/manage")]
    #[NoReturn] private function adminUsersEditPicturePost(Request $request, int $id): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.manage.edit");

        $image = $_FILES['profilePicture'];
        $this->getHighestImplementation(IUsersProfilePicture::class)->changeMethod($image, $id);
    }

    #[Link("/picture/reset/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/users/manage")]
    #[NoReturn] private function adminUsersResetPicture(Request $request, int $id): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

        $this->getHighestImplementation(IUsersProfilePicture::class)->resetPicture($id);
    }

    // PUBLIC SECTION

    #[Link('/login', Link::POST)]
    private function loginPost(): void
    {
        if (!SecurityController::checkCaptcha()) {
            Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),
                'Captcha invalide.');
            Redirect::redirectPreviousRoute();
        }

        [$mail, $password, $previousRoute] = Utils::filterInput("login_email", "login_password", "previousRoute");

        if (Utils::containsNullValue($mail, $password)) {
            Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),
                LangManager::translate("core.toaster.db.missing_inputs"));
            Redirect::redirectPreviousRoute();
        }

        $encryptedMail = EncryptManager::encrypt(mb_strtolower($mail));

        $cookie = isset($_POST['login_keep_connect']) && $_POST['login_keep_connect'] ? 1 : 0;

        $loginStatus = $this->checkLogin($encryptedMail, $password);

        switch ($loginStatus) {
            case LoginStatus::NOT_FOUND:
                Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                    LangManager::translate("users.toaster.not_registered_account"));
                Redirect::redirectPreviousRoute();
            case LoginStatus::NOT_MATCH:
                Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                    LangManager::translate("users.toaster.mail_pass_matching"));
                Redirect::redirectPreviousRoute();
            case LoginStatus::INTERNAL_ERROR:
                Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                    LangManager::translate("core.toaster.internalError"));
                Redirect::redirectPreviousRoute();
            case LoginStatus::OK:
                $user = UsersModel::getInstance()->getUserWithMail($encryptedMail);
                if (is_null($user)) {
                    Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                        LangManager::translate("core.toaster.internalError"));
                    Redirect::redirectPreviousRoute();
                }
                $this->loginUser($user, $cookie);
                if ($previousRoute) {
                    Redirect::redirectPreviousRoute();
                } else {
                    Redirect::redirect("profile");
                }
                break;
            case LoginStatus::OK_NEED_2FA:
                $user = UsersModel::getInstance()->getUserWithMail($encryptedMail);
                if (is_null($user)) {
                    Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                        LangManager::translate("core.toaster.internalError"));
                    Redirect::redirectPreviousRoute();
                }

                $_SESSION['cmw_temp_user_id'] = $user->getId();
                $_SESSION['cmw_temp_use_cookies'] = $cookie;

                $this->showLogin2Fa();
        }
    }

    /**
     * @throws \CMW\Manager\Router\RouterException
     */
    #[Link('/login', Link::GET)]
    private function loginGet(): void
    {
        if (self::isUserLogged()) {
            Redirect::redirectToHome();
        }


        $view = new View("Users", "login");
        $view->view();
    }

    private function showLogin2Fa(): void
    {
        $view = new View("Users", "2fa");
        $view->view();
    }

    #[Link('/login/validate/tfa', Link::POST)]
    private function loginCheck2Fa(): void
    {
        if (!isset($_POST['code'])) {
            Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                'Merci de mettre votre code.');
            $this->showLogin2Fa();
            return;
        }

        $code = FilterManager::filterInputIntPost('code', 6);

        if (strlen($code) !== 6) {
            Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),
                "Code invalide.");
            $this->showLogin2Fa();
            return;
        }


        if (!isset($_SESSION['cmw_temp_user_id'])) {
            Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            $this->showLogin2Fa();
            return;
        }

        $user = UsersModel::getInstance()->getUserById($_SESSION['cmw_temp_user_id']);

        if (is_null($user)) {
            Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            $this->showLogin2Fa();
            return;
        }

        $tfa = new TwoFaManager();
        if (!$tfa->isSecretValid($user->get2Fa()->get2FaSecretDecoded(), $code)) {
            Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),
                "Code invalide.");
            $this->showLogin2Fa();
            return;
        }

        $useCookies = isset($_SESSION['cmw_temp_use_cookies']) ? $_SESSION['cmw_temp_use_cookies'] : 0;

        $this->loginUser($user, $useCookies);

        //Clean temp sessions
        unset($_SESSION['cmw_temp_user_id'],
            $_SESSION['cmw_temp_use_cookies']);

        //Redirect
        Redirect::redirect("profile");


    }

    /**
     * @throws \CMW\Manager\Router\RouterException
     */
    #[Link('/login/forgot', Link::GET)]
    private function forgotPassword(): void
    {
        if (self::isUserLogged()) {
            Redirect::redirectToHome();
        }

        $view = new View("Users", "forgot_password");
        $view->view();
    }


    #[NoReturn] #[Link('/login/forgot', Link::POST)]
    private function forgotPasswordPost(): void
    {
        if (SecurityController::checkCaptcha()) {
            $mail = filter_input(INPUT_POST, "mail");

            $encryptedMail = EncryptManager::encrypt($mail);

            //We check if this email exist
            if (UsersModel::getInstance()->checkEmail($encryptedMail) <= 0) {
                Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                    LangManager::translate('users.toaster.not_registered_account'));

                Redirect::redirectPreviousRoute();
            }
            //We send a verification link for this mail
            UsersModel::getInstance()->resetPassword($encryptedMail);

            Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
                LangManager::translate('users.toaster.password_reset', ['mail' => $mail]));

            if (str_starts_with($_SERVER['HTTP_REFERER'], EnvManager::getInstance()->getValue('PATH_URL') . 'cmw-admin/')) {
                Redirect::redirectPreviousRoute();
            }

            Redirect::redirect("login");
        } else {
            //TODO Toaster invalid captcha
            Redirect::redirectPreviousRoute();
        }
    }

    /**
     * @throws \CMW\Manager\Router\RouterException
     */
    #[Link('/register', Link::GET)]
    private function register(): void
    {
        if (self::isUserLogged()) {
            Redirect::redirectToHome();
        }

        $view = new View("Users", "register");
        $view->view();
    }

    #[NoReturn] #[Link('/register', Link::POST)]
    private function registerPost(): void
    {
        if (SecurityController::checkCaptcha()) {
            $mail = FilterManager::filterInputStringPost("register_email");
            $encryptedMail = EncryptManager::encrypt(mb_strtolower($mail));
            if (UsersModel::getInstance()->checkPseudo(FilterManager::filterInputStringPost("register_pseudo")) > 0) {
                Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),
                    LangManager::translate("users.toaster.used_pseudo"));
                Redirect::redirect('register');
            } else if (!FilterManager::isEmail($mail) || UsersModel::getInstance()->checkEmail($encryptedMail) > 0) {
                Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),
                    LangManager::translate("users.toaster.used_mail"));
                Redirect::redirect('register');
            } else if (UsersSettingsModel::getInstance()->isPseudoBlacklisted(filter_input(INPUT_POST, "register_pseudo"))) {
                Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),
                    LangManager::translate("users.toaster.blacklisted_pseudo"));
                Redirect::redirect('register');
            } else {
                [$pseudo, $password, $passwordVerify] = Utils::filterInput("register_pseudo", "register_password", "register_password_verify");

                if (!is_null($password) && $password !== $passwordVerify) {
                    Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),
                        LangManager::translate("users.toaster.not_same_pass"));
                    Redirect::redirect('register');
                }

                $defaultRoles = RolesModel::getInstance()->getDefaultRoles();
                $defaultRolesId = [];

                foreach ($defaultRoles as $role) {
                    $defaultRolesId[] = $role->getId();
                }

                $userEntity = UsersModel::getInstance()->create($encryptedMail, $pseudo, "", "", $defaultRolesId);

                $userId = $userEntity?->getId();

                Emitter::send(RegisterEvent::class, $userId);

                UsersModel::getInstance()->updatePass($userEntity?->getId(), password_hash($password, PASSWORD_BCRYPT));


                /* Connection */
                $loginCheck = $this->checkLogin($encryptedMail, $password);

                if ($loginCheck->name === LoginStatus::OK->name && !is_null($userEntity)) {
                    $this->loginUser($userEntity, 1);

                    Flash::send(Alert::SUCCESS, LangManager::translate("users.toaster.success"),
                        LangManager::translate("users.toaster.welcome"));
                    Redirect::redirect('profile');
                } else {
                    Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                        LangManager::translate("core.toaster.internalError"));
                    Redirect::redirectPreviousRoute();
                }
            }
        }
    }

    /**
     * @throws \CMW\Manager\Router\RouterException
     */
    #[Link('/profile', Link::GET)]
    private function publicProfile(): void
    {
        if (!self::isUserLogged() && !UserSettingsEntity::getInstance()->isProfilePageEnabled()) {
            Redirect::redirect('login');
        }

        if (!UserSettingsEntity::getInstance()->isProfilePageEnabled()) {
            Redirect::redirectToHome();
        }

        if (!self::isUserLogged()) {
            Redirect::redirect('login');
        }

        $user = UsersModel::getCurrentUser();

        if (UsersModel::getCurrentUser()?->getId() !== $user->getId()) {
            Flash::send(Alert::ERROR,LangManager::translate("core.toaster.error"),"Vous ne pouvez pas éditer le profile de quelqu'un d'autre !" );
            Redirect::redirectToHome();
        }

        if (UserSettingsEntity::getInstance()->getProfilePageStatus() === 1) {
            Redirect::redirect("profile/", ['pseudo' => $user?->getPseudo()]);
        }

        $view = new View('Users', 'profile');
        $view->addVariableList(["user" => $user]);
        $view->view();
    }

    #[Link('/profile', Link::POST)]
    private function publicProfilePost(): void
    {
        if (!UserSettingsEntity::getInstance()->isProfilePageEnabled()) {
            Redirect::redirectToHome();
        }

        if (!self::isUserLogged()) {
            Redirect::redirectToHome();
        }

        $image = $_FILES['pictureProfile'];

        try {
            UserPictureModel::getInstance()->uploadImage(UsersModel::getCurrentUser()?->getId(), $image);
        } catch (Exception $e) {
            Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError") . " => $e");
        }

        Redirect::redirect('profile');
    }

    #[Link('/profile/:pseudo', Link::GET, ['pseudo' => '.*?'])]
    private function publicProfileWithPseudo(Request $request, string $pseudo): void
    {
        if (!self::isUserLogged() && !UserSettingsEntity::getInstance()->isProfilePageEnabled()) {
            Redirect::redirect('login');
        }

        if (!UserSettingsEntity::getInstance()->isProfilePageEnabled()) {
            Redirect::redirectToHome();
        }

        if (UserSettingsEntity::getInstance()->getProfilePageStatus() === 0) {
            Redirect::redirect("profile");
        }

        $user = UsersModel::getInstance()->getUserWithPseudo($pseudo);

        if (UsersModel::getCurrentUser()?->getId() !== $user->getId()) {
            Flash::send(Alert::ERROR,LangManager::translate("core.toaster.error"),"Vous ne pouvez pas éditer le profile de quelqu'un d'autre !" );
            Redirect::redirectToHome();
        }

        if (is_null($user)) {
            Redirect::errorPage(404);
        }

        $view = new View('Users', 'profile');
        $view->addVariableList(["user" => $user]);
        $view->view();
    }

    #[NoReturn] #[Link("/profile/delete/:id", Link::GET, ["id" => "[0-9]+"])]
    private function publicProfileDelete(Request $request, int $id): void
    {
        //Check if this is the current user account
        if (UsersModel::getCurrentUser()?->getId() !== $id) {
            //TODO ERROR MANAGEMENT (MESSAGE TO TELL THE USER CAN'T DELETE THIS ACCOUNT)
            Redirect::errorPage(403);
        }

        Emitter::send(DeleteUserAccountEvent::class, $id);

        UsersModel::logOut();
        UsersModel::getInstance()->delete($id);

        Redirect::redirectToHome();
    }

    #[NoReturn] #[Link('/logout', Link::GET)]
    private function logOut(): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();
        Emitter::send(LogoutEvent::class, $userId);
        UsersModel::logOut();
        Redirect::redirectToHome();
    }

    #[NoReturn] #[Link('/profile/update', Link::POST)]
    private function publicProfileUpdate(): void
    {
        if (!self::isUserLogged()) {
            Redirect::redirectToHome();
        }

        $user = UsersModel::getCurrentUser();

        [$mail, $pseudo, $firstname, $lastname] = Utils::filterInput("email", "pseudo", "name", "lastname");

        if (UsersSettingsModel::getInstance()->isPseudoBlacklisted($pseudo)) {
            Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),
                LangManager::translate("users.toaster.blacklisted_pseudo"));
            Redirect::redirectPreviousRoute();
        }

        $roles = UsersModel::getRoles($user?->getId());

        $rolesId = [];

        foreach ($roles as $role) {
            $rolesId[] = $role->getId();
        }

        UsersModel::getInstance()->update($user?->getId(), $mail, $pseudo, $firstname, $lastname, $rolesId);


        [$pass, $passVerif] = Utils::filterInput("password", "passwordVerif");

        if (!is_null($pass)) {
            if ($pass === $passVerif) {
                UsersModel::getInstance()->updatePass($user?->getId(), password_hash($pass, PASSWORD_BCRYPT));
            } else {
                //Todo Try to edit that
                Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"), "Je sais pas ?");
            }
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/profile/2fa/toggle', Link::POST)]
    private function publicProfile2FaToggle(): void
    {

        $user = UsersModel::getCurrentUser();

        if (is_null($user)) {
            Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            Redirect::redirectToHome();
        }

        if (!isset($_POST['secret'])) {
            Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),
                "Merci de remplir le code d'authentification");
            return;
        }

        $secret = FilterManager::filterInputIntPost('secret', 6);

        if (strlen($secret) !== 6) {
            Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),
                "Code invalide.");
            Redirect::redirectPreviousRoute();
        }

        $tfa = new TwoFaManager();
        if (!$tfa->isSecretValid($user->get2Fa()->get2FaSecretDecoded(), $secret)) {
            Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),
                "Code invalide.");
            Redirect::redirectPreviousRoute();
        }

        $status = $user->get2Fa()->isEnabled() ? 0 : 1;

        if (Users2FaModel::getInstance()->toggle2Fa($user->getId(), $status)) {
            UsersModel::updateStoredUser($user->getId());
            Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
                $status ? '2fa activée' : '2fa désactivée');
        } else {
            Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
                LangManager::translate("core.toaster.internalError"));
        }

        Redirect::redirectPreviousRoute();
    }
}

//TODO Use different file (need to change autoloader)
enum LoginStatus
{
    case NOT_FOUND;
    case NOT_MATCH;
    case INTERNAL_ERROR;
    case OK;
    case OK_NEED_2FA;
}

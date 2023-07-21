<?php

namespace CMW\Controller\Users;

use CMW\Controller\Core\SecurityController;
use CMW\Entity\Users\UserSettingsEntity;
use CMW\Interface\Users\IUsersProfilePicture;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Loader\Loader;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Security\EncryptManager;
use CMW\Manager\Views\View;
use CMW\Model\Users\RolesModel;
use CMW\Model\Users\UserPictureModel;
use CMW\Model\Users\UsersModel;
use CMW\Model\Users\UsersSettingsModel;
use CMW\Utils\Log;
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

            if ($weight > $highestWeight){
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

    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin/users")]
    #[Link("/manage", Link::GET, [], "/cmw-admin/users")]
    private function adminUsersList(): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

        $userList = UsersModel::getInstance()->getUsers();
        $roles = RolesModel::getInstance()->getRoles();


        View::createAdminView("Users", "manage")
            ->addVariableList(["userList" => $userList, "roles" => $roles])
            ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css",
                "Admin/Resources/Assets/Css/Pages/simple-datatables.css")
            ->addScriptBefore("App/Package/Users/Views/Assets/Js/edit.js")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/Umd/simple-datatables.js",
                "Admin/Resources/Assets/Js/Pages/simple-datatables.js")
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
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

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
            "pictureLink" => $user?->getUserPicture()?->getImageLink(),
            "pictureLastUpdate" => $user?->getUserPicture()?->getLastUpdate(),
            "userHighestRole" => $user?->getHighestRole()?->getName(),
            "roles" => $roles
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
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

        $userEntity = UsersModel::getInstance()->getUserById($id);

        $roles = RolesModel::getInstance()->getRoles();

        View::createAdminView("Users", "user")->addVariableList(array(
            "user" => $userEntity,
            "roles" => $roles
        ))
            ->view();
    }

    #[Link("/edit/:id", Link::POST, ["id" => "[0-9]+"], "/cmw-admin/users/manage")]
    #[NoReturn] private function adminUsersEditPost(Request $request, int $id): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

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
        self::redirectIfNotHavePermissions("core.dashboard", "users.add");

        [$mail, $pseudo, $firstname, $lastname] = Utils::filterInput("email", "pseudo", "firstname", "surname");

        $encryptedMail = EncryptManager::encrypt($mail);

        $userEntity = UsersModel::getInstance()->create($encryptedMail, $pseudo, $firstname, $lastname, $_POST['roles']);

        if ($userEntity === null) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('users.toaster.error_add'));
            Redirect::redirectPreviousRoute();
        }

        UsersModel::getInstance()->updatePass($userEntity?->getId(), password_hash(filter_input(INPUT_POST, "password"), PASSWORD_BCRYPT));

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('users.toaster.success_add', ['pseudo' => $pseudo]));

        Redirect::redirectPreviousRoute();
    }

    #[Link("/manage/state/:id/:state", Link::GET, ["id" => "[0-9]+", "state" => "[0-9]+"], "/cmw-admin/users")]
    #[NoReturn] private function adminUserState(Request $request, int $id, int $state): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

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
        self::redirectIfNotHavePermissions("core.dashboard", "users.delete");

        if (UsersModel::getCurrentUser()?->getId() === $id) {

            //Todo Try to remove that
            Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),
                LangManager::translate("users.toaster.impossible_user"));
            Redirect::redirectPreviousRoute();
        }

        UsersModel::getInstance()->delete($id);

        //Todo Try to remove that
        Flash::send(Alert::SUCCESS, LangManager::translate("users.toaster.success"),
            LangManager::translate("users.toaster.user_deleted"));

        Redirect::redirectPreviousRoute();
    }

    #[Link("/picture/edit/:id", Link::POST, ["id" => "[0-9]+"], "/cmw-admin/users/manage")]
    #[NoReturn] private function adminUsersEditPicturePost(Request $request, int $id): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

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
        if (SecurityController::checkCaptcha()) {

            [$mail, $password, $previousRoute] = Utils::filterInput("login_email", "login_password", "previousRoute");

            if (Utils::containsNullValue($mail, $password)) {
                Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),
                    LangManager::translate("core.toaster.db.missing_inputs"));
                Redirect::redirectPreviousRoute();
            }

            $encryptedMail = EncryptManager::encrypt($mail);

            $infos = [
                "email" => $encryptedMail,
                "password" => $password
            ];

            $cookie = isset($_POST['login_keep_connect']) && $_POST['login_keep_connect'] ? 1 : 0;

            $userId = UsersModel::logIn($infos, $cookie);
            if ($userId > 0) {
                UsersModel::getInstance()->updateLoggedTime($userId);
                if ($previousRoute) {
                    header('Location: ' . $previousRoute);
                    /*$previousRouteStr = str_replace(Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER"), "", $previousRoute);
                    Redirect::redirect($previousRouteStr);*/
                } else {
                    Redirect::redirect("profile");
                }
            } else if ($userId === -1) {
                Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                    LangManager::translate("users.toaster.mail_pass_matching"));
                Redirect::redirectPreviousRoute();
            } else if ($userId === -2) {
                Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                    LangManager::translate("users.toaster.not_registered_account"));
                Redirect::redirectPreviousRoute();
            } else {
                Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                    LangManager::translate("core.toaster.internalError"));
                Redirect::redirectPreviousRoute();
            }
        } else {
            //TODO Toaster invalid captcha
            Redirect::redirectPreviousRoute();
        }

    }

    /**
     * @throws \CMW\Manager\Router\RouterException
     */
    #[Link('/login', Link::GET)]
    private function login(): void
    {
        if (self::isUserLogged()) {
            Redirect::redirectToHome();
        }


        $view = new View("Users", "login");
        $view->view();
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
            $encryptedMail = EncryptManager::encrypt(filter_input(INPUT_POST, "register_email"));
            if (UsersModel::getInstance()->checkPseudo(filter_input(INPUT_POST, "register_pseudo")) > 0) {
                Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),
                    LangManager::translate("users.toaster.used_pseudo"));
                Redirect::redirect('register');
            } else if (UsersModel::getInstance()->checkEmail($encryptedMail) > 0) {
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

                UsersModel::getInstance()->updatePass($userEntity?->getId(), password_hash($password, PASSWORD_BCRYPT));


                /* Connection */

                $infos = array(
                    "email" => $encryptedMail,
                    "password" => $password
                );

                $userId = UsersModel::logIn($infos, 1);
                if ($userId > 0) {
                    UsersModel::getInstance()->updateLoggedTime($userId);

                    Flash::send(Alert::SUCCESS, LangManager::translate("users.toaster.success"),
                        LangManager::translate("users.toaster.welcome"));
                    Redirect::redirect('profile');
                } else if ($userId === -1) {
                    Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                        LangManager::translate("users.toaster.mail_pass_matching"));
                    Redirect::redirectPreviousRoute();
                } else if ($userId === -2) {
                    Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                        LangManager::translate("users.toaster.not_registered_account"));
                    Redirect::redirectPreviousRoute();
                } else {
                    Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                        LangManager::translate("core.toaster.internalError"));
                    Redirect::redirectPreviousRoute();
                }

            }
        } else {
            //TODO Toaster invalid captcha
            Redirect::redirectPreviousRoute();
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

        UsersModel::logOut();
        UsersModel::getInstance()->delete($id);

        Redirect::redirectToHome();
    }

    #[NoReturn] #[Link('/logout', Link::GET)]
    private function logOut(): void
    {
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

        $rolesId = array();

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

}

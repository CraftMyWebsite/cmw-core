<?php

namespace CMW\Controller\Users;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Core\SecurityController;
use CMW\Entity\Users\UserEntity;
use CMW\Entity\Users\UserSettingsEntity;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Users\RolesModel;
use CMW\Model\Users\UserPictureModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use JsonException;

/**
 * Class: @usersController
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class UsersController extends AbstractController
{
    public function adminDashboard(): void
    {
        header("Location" . getenv("PATH_SUBFOLDER") . ((self::isAdminLogged()) ? "cmw-admin/dashboard" : "login"));
    }

    public static function isAdminLogged(): bool
    {
        return UsersModel::hasPermission(self::getSessionUser(), "core.dashboard");
    }

    /**
     * @return bool
     * @desc Return true if the current user / client is logged.
     */
    public static function isUserLogged(): bool
    {
        return isset($_SESSION['cmwUserId']);
    }

    public static function hasPermission(string ...$permissions): bool
    {
        return UsersModel::hasPermission(self::getSessionUser(), ...$permissions);
    }

    private static function getSessionUser(): ?UserEntity
    {
        if (!isset($_SESSION['cmwUserId'])) {
            return null;
        }

        return (UsersModel::getInstance())->getUserById($_SESSION['cmwUserId']);
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
            ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css","Admin/Resources/Assets/Css/Pages/simple-datatables.css")
            ->addScriptBefore("App/Package/users/Views/Assets/Js/edit.js")
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

        foreach ($user?->getRoles() as $role){
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

        [$mail, $username, $firstname, $lastname] = Utils::filterInput("email", "pseudo", "name", "lastname");
        UsersModel::getInstance()->update($id, $mail, $username, $firstname, $lastname, $_POST['roles']);

        //Todo Try to edit that
        Flash::send(Alert::SUCCESS, LangManager::translate("users.toaster.success"),LangManager::translate("users.toaster.user_edited"));

        [$pass, $passVerif] = Utils::filterInput("pass", "passVerif");

        if (!is_null($pass)) {
            if ($pass === $passVerif) {
                UsersModel::getInstance()->updatePass($id, password_hash($pass, PASSWORD_BCRYPT));
            } else {
                Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),LangManager::translate("users.toaster.pass_change_faild"));

            }

        }

        header("location: ../edit/" . $id); //Todo redirect
    }


    #[Link("/add", Link::POST, [], "/cmw-admin/users")]
    private function adminUsersAddPost(): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.add");

        [$mail, $username, $firstname, $lastname] = Utils::filterInput("email", "pseudo", "firstname", "surname");

        $userEntity = UsersModel::getInstance()->create($mail, $username, $firstname, $lastname, $_POST['roles']);

        UsersModel::getInstance()->updatePass($userEntity?->getId(), password_hash(filter_input(INPUT_POST, "password"), PASSWORD_BCRYPT));

        header("location: " . $_SERVER['HTTP_REFERER']); //Todo redirect
    }

    #[Link("/state/:id/:state", Link::GET, ["id" => "[0-9]+", "state" => "[0-9]+"], "/cmw-admin/users")]
    #[NoReturn] private function adminUserState(Request $request, int $id, int $state): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

        if (UsersModel::getLoggedUser() === $id) {
            Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),LangManager::translate("users.toaster.impossible"));
            header('Location: ' . $_SERVER['HTTP_REFERER']);  //Todo redirect
            die();
        }

        $state = ($state) ? 0 : 1;

        UsersModel::getInstance()->changeState($id, $state);

        Flash::send(Alert::SUCCESS, LangManager::translate("users.toaster.success"),"Ok !");

        header("location: " . $_SERVER['HTTP_REFERER']);  //Todo redirect
    }

    #[Link("/delete/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/users")]
    #[NoReturn] private function adminUsersDelete(Request $request, int $id): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.delete");

        if (UsersModel::getLoggedUser() === $id) {

            //Todo Try to remove that
            Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),LangManager::translate("users.toaster.impossible_user"));
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            die();
        }

        UsersModel::getInstance()->delete($id);

        //Todo Try to remove that
        Flash::send(Alert::SUCCESS, LangManager::translate("users.toaster.success"),LangManager::translate("users.toaster.user_deleted"));

        header("location: " . $_SERVER['HTTP_REFERER']);
    }

    #[Link("/picture/edit/:id", Link::POST, ["id" => "[0-9]+"], "/cmw-admin/users")]
    #[NoReturn] private function adminUsersEditPicturePost(Request $request, int $id): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

        $image = $_FILES['profilePicture'];


        UserPictureModel::getInstance()->uploadImage($id, $image);

        header("location: " . $_SERVER['HTTP_REFERER']);  //Todo redirect
    }

    #[Link("/picture/reset/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/users")]
    #[NoReturn] private function adminUsersResetPicture(Request $request, int $id): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

        UserPictureModel::getInstance()->deleteUserPicture($id);

        header("location: " . $_SERVER['HTTP_REFERER']);  //Todo redirect
    }

    // PUBLIC SECTION

    #[Link('/login', Link::POST)]
    private function loginPost(): void
    {
        if(SecurityController::checkCaptcha()) {

            [$mail, $password] = Utils::filterInput("login_email", "login_password");

            $infos = array(
                "email" => $mail,
                "password" => $password
            );
            $cookie = 0;

            if (isset($_POST['login_keep_connect']) && $_POST['login_keep_connect']) {
                $cookie = 1;
            }

            $userId = UsersModel::logIn($infos, $cookie);
            if ($userId > 0 && $userId !== "ERROR") {
                UsersModel::getInstance()->updateLoggedTime($userId);
                header('Location: ' . getenv('PATH_SUBFOLDER') . 'profile');

            } else {
                Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),LangManager::translate("users.toaster.mail_pass_matching"));
                Redirect::redirectToPreviousPage();
            }
        } else {
            //TODO Toaster invalid captcha
            Redirect::redirectToPreviousPage();
        }
    }

    /**
     * @throws \CMW\Manager\Router\RouterException
     */
    #[Link('/login', Link::GET)]
    private function login(): void
    {
        if (UsersModel::getLoggedUser() !== -1) {
            header('Location: ' . getenv('PATH_SUBFOLDER'));  //Todo redirect
            die();
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
        if (UsersModel::getLoggedUser() !== -1) {
            header('Location: ' . getenv('PATH_SUBFOLDER'));  //Todo redirect
            die();
        }

        $view = new View("Users", "forgot_password");
        $view->view();
    }


    #[Link('/login/forgot', Link::POST)]
    private function forgotPasswordPost(): void
    {
        $mail = filter_input(INPUT_POST, "mail");

        //We check if this email exist
        if(UsersModel::getInstance()->checkEmail($mail) <= 0) {
            //TODO toaster with error
            die();
        }

        //We send a verification link for this mail
        UsersModel::getInstance()->resetPassword($mail);
        header("Location: /login");
    }

    /**
     * @throws \CMW\Manager\Router\RouterException
     */
    #[Link('/register', Link::GET)]
    private function register(): void
    {
        if (UsersModel::getLoggedUser() !== -1) {
            header('Location: ' . getenv('PATH_SUBFOLDER'));  //Todo redirect
            die();
        }

        $view = new View("Users", "register");
        $view->view();
    }

    #[Link('/register', Link::POST)]
    private function registerPost(): void
    {
        if(SecurityController::checkCaptcha()) {
        if (UsersModel::getInstance()->checkPseudo(filter_input(INPUT_POST, "register_pseudo")) > 0) {
            Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),LangManager::translate("users.toaster.used_pseudo"));
            header('Location: register');
        } else if (UsersModel::getInstance()->checkEmail(filter_input(INPUT_POST, "register_email")) > 0) {
            Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),LangManager::translate("users.toaster.used_mail"));
            header('Location: register');
        } else {

            [$mail, $pseudo, $password, $passwordVerify] = Utils::filterInput("register_email", "register_pseudo", "register_password", "register_password_verify");

            if (!is_null($password) && $password !== $passwordVerify) {
                Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),LangManager::translate("users.toaster.not_same_pass"));
                header('Location: register');
            }

            $defaultRoles = RolesModel::getInstance()->getDefaultRoles();
            $defaultRolesId = [];

            foreach ($defaultRoles as $role){
                $defaultRolesId[] = $role->getId();
            }

            $userEntity = UsersModel::getInstance()->create($mail, $pseudo, "", "", $defaultRolesId);

            UsersModel::getInstance()->updatePass($userEntity?->getId(), password_hash($password, PASSWORD_BCRYPT));


            /* Connection */

            $infos = array(
                "email" => filter_input(INPUT_POST, "register_email"),
                "password" => filter_input(INPUT_POST, "register_password")
            );

            $cookie = 1;

            $userId = UsersModel::logIn($infos, $cookie);
            if ($userId > 0 && $userId !== "ERROR") {
                UsersModel::getInstance()->updateLoggedTime($userId);
                header('Location: ' . getenv('PATH_SUBFOLDER') . 'profile');


                Flash::send(Alert::SUCCESS, LangManager::translate("users.toaster.success"),LangManager::translate("users.toaster.welcome"));

            }

        }
        } else {
            //TODO Toaster invalid captcha
            header('Location: ' . $_SERVER['HTTP_REFERER']);  //Todo redirect
        }

    }

    /**
     * @throws \CMW\Manager\Router\RouterException
     */
    #[Link('/profile', Link::GET)]
    private function publicProfile(): void
    {
        if (UsersModel::getLoggedUser() === -1 && !UserSettingsEntity::getInstance()->isProfilePageEnabled()){
            Redirect::redirect('login');
            return;
        }

        if (!UserSettingsEntity::getInstance()->isProfilePageEnabled()){
            Redirect::redirectToHome();
            return;
        }

        if (UsersModel::getLoggedUser() === -1) {
            Redirect::redirect('login');
            return;
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
        if (!UserSettingsEntity::getInstance()->isProfilePageEnabled()){
            Redirect::redirectToHome();
            return;
        }

        if (UsersModel::getLoggedUser() === -1) {
            Redirect::redirectToHome();
            return;
        }

        $image = $_FILES['pictureProfile'];

        try {
            UserPictureModel::getInstance()->uploadImage($_SESSION['cmwUserId'], $image);
        } catch (Exception $e) {
            Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError") . " => $e");
        }

        header('Location: ' . getenv('PATH_SUBFOLDER') . 'profile');  //Todo redirect
    }

    #[Link('/profile/:pseudo', Link::GET, ['pseudo' => '.*?'])]
    private function publicProfileWithPseudo(Request $request, string $pseudo): void
    {
        if (!UserSettingsEntity::getInstance()->isProfilePageEnabled() && UsersModel::getLoggedUser() === -1){
            Redirect::redirect('login');
            return;
        }

        if (!UserSettingsEntity::getInstance()->isProfilePageEnabled()){
            Redirect::redirectToHome();
            return;
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

    #[Link("/profile/delete/:id", Link::GET, ["id" => "[0-9]+"])]
    private function publicProfileDelete(Request $request, int $id): void
    {
        //Check if this is the current user account
        if ($_SESSION['cmwUserId'] !== $id) {
            //TODO ERROR MANAGEMENT (MESSAGE TO TELL THE USER CAN'T DELETE THIS ACCOUNT)
            header('Location: ' . getenv('PATH_SUBFOLDER') . 'profile');  //Todo redirect
            return;
        }

        UsersModel::logOut();
        UsersModel::getInstance()->delete($id);

        header('Location: ' . getenv('PATH_SUBFOLDER'));  //Todo redirect
    }

    #[Link('/logout', Link::GET)]
    private function logOut(): void
    {
        UsersModel::logOut();
        header('Location: ' . getenv('PATH_SUBFOLDER'));  //Todo redirect
    }

    #[Link('/profile/update', Link::POST)]
    private function publicProfileUpdate(): void
    {
        if (!isset($_SESSION['cmwUserId'])) {
            header('Location: ' . getenv('PATH_SUBFOLDER'));
            return;
        }

        $userId = $_SESSION['cmwUserId'];

        [$mail, $username, $firstname, $lastname] = Utils::filterInput("email", "pseudo", "name", "lastname");

        $roles = UsersModel::getRoles($userId);

        $rolesId = array();

        foreach ($roles as $role){
            $rolesId[] = $role->getId();
        }

        UsersModel::getInstance()->update($userId, $mail, $username, $firstname, $lastname, $rolesId);


        [$pass, $passVerif] = Utils::filterInput("password", "passwordVerif");

        if (!is_null($pass)) {
            if ($pass === $passVerif) {
                UsersModel::getInstance()->updatePass($userId, password_hash($pass, PASSWORD_BCRYPT));
            } else {
                //Todo Try to edit that
                Flash::send(Alert::ERROR, LangManager::translate("users.toaster.error"),"Je sais pas ?");
            }
        }

        header('Location: ' . getenv('PATH_SUBFOLDER') . 'profile');  //Todo redirect
    }

}
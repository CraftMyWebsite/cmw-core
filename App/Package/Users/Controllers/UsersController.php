<?php

namespace CMW\Controller\Users;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Core\SecurityController;
use CMW\Entity\Users\UserEntity;
use CMW\Entity\Users\UserSettingsEntity;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Users\RolesModel;
use CMW\Model\Users\UserPictureModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use CMW\Utils\Response;
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
class UsersController extends CoreController
{
    private UsersModel $userModel;
    private RolesModel $roleModel;
    private UserPictureModel $userPictureModel;
    private UserSettingsEntity $userSettingsEntity;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UsersModel();
        $this->roleModel = new RolesModel();
        $this->userPictureModel = new UserPictureModel();
        $this->userSettingsEntity = new UserSettingsEntity();
    }

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

        return (new UsersModel())->getUserById($_SESSION['cmwUserId']);
    }

    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin/users")]
    #[Link("/manage", Link::GET, [], "/cmw-admin/users")]
    public function adminUsersList(): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

        $userList = $this->userModel->getUsers();
        $roles = $this->roleModel->getRoles();


        View::createAdminView("users", "manage")
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
    public function admingetUser(Request $request, int $id): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

        $user = (new UsersModel())->getUserById($id);

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


    #[Link("/edit/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/users")]
    public function adminUsersEdit(Request $request, int $id): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

        Utils::debugR($request);

        $userEntity = $this->userModel->getUserById($id);

        $roles = $this->roleModel->getRoles();

        View::createAdminView("users", "user")->addVariableList(array(
            "user" => $userEntity,
            "roles" => $roles
        ))
            ->view();
    }

    #[Link("/edit/:id", Link::POST, ["id" => "[0-9]+"], "/cmw-admin/users")]
    #[NoReturn] public function adminUsersEditPost(Request $request, int $id): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

        [$mail, $username, $firstname, $lastname] = Utils::filterInput("email", "pseudo", "name", "lastname");
        $this->userModel->update($id, $mail, $username, $firstname, $lastname, $_POST['roles']);

        //Todo Try to edit that
        Response::sendAlert("success", LangManager::translate("users.toaster.success"),LangManager::translate("users.toaster.user_edited"));

        [$pass, $passVerif] = Utils::filterInput("pass", "passVerif");

        if (!is_null($pass)) {
            if ($pass === $passVerif) {
                $this->userModel->updatePass($id, password_hash($pass, PASSWORD_BCRYPT));
            } else {
                Response::sendAlert("error", LangManager::translate("users.toaster.error"),LangManager::translate("users.toaster.pass_change_faild"));

            }

        }

        header("location: ../edit/" . $id);
    }


    #[Link("/add", Link::POST, [], "/cmw-admin/users")]
    public function adminUsersAddPost(): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.add");

        [$mail, $username, $firstname, $lastname] = Utils::filterInput("email", "pseudo", "firstname", "surname");

        $userEntity = $this->userModel->create($mail, $username, $firstname, $lastname, $_POST['roles']);

        $this->userModel->updatePass($userEntity?->getId(), password_hash(filter_input(INPUT_POST, "password"), PASSWORD_BCRYPT));

        header("location: " . $_SERVER['HTTP_REFERER']);
    }

    #[Link("/state/:id/:state", Link::GET, ["id" => "[0-9]+", "state" => "[0-9]+"], "/cmw-admin/users")]
    #[NoReturn] public function adminUserState(Request $request, int $id, int $state): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

        if (UsersModel::getLoggedUser() === $id) {
            Response::sendAlert("error", LangManager::translate("users.toaster.error"),LangManager::translate("users.toaster.impossible"));
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            die();
        }

        $state = ($state) ? 0 : 1;

        $this->userModel->changeState($id, $state);

        Response::sendAlert("success", LangManager::translate("users.toaster.success"),"Ok !");

        header("location: " . $_SERVER['HTTP_REFERER']);
    }

    #[Link("/delete/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/users")]
    #[NoReturn] public function adminUsersDelete(Request $request, int $id): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.delete");

        if (UsersModel::getLoggedUser() === $id) {

            //Todo Try to remove that
            Response::sendAlert("error", LangManager::translate("users.toaster.error"),LangManager::translate("users.toaster.impossible_user"));
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            die();
        }

        $this->userModel->delete($id);

        //Todo Try to remove that
        Response::sendAlert("success", LangManager::translate("users.toaster.success"),LangManager::translate("users.toaster.user_deleted"));

        header("location: " . $_SERVER['HTTP_REFERER']);
    }

    #[Link("/picture/edit/:id", Link::POST, ["id" => "[0-9]+"], "/cmw-admin/users")]
    #[NoReturn] public function adminUsersEditPicturePost(Request $request, int $id): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

        $image = $_FILES['profilePicture'];


        $this->userPictureModel->uploadImage($id, $image);

        header("location: " . $_SERVER['HTTP_REFERER']);
    }

    #[Link("/picture/reset/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/users")]
    #[NoReturn] public function adminUsersResetPicture(Request $request, int $id): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

        $this->userPictureModel->deleteUserPicture($id);

        header("location: " . $_SERVER['HTTP_REFERER']);
    }

    // PUBLIC SECTION

    #[Link('/login', Link::POST)]
    public function loginPost(): void
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
                $this->userModel->updateLoggedTime($userId);
                header('Location: ' . getenv('PATH_SUBFOLDER') . 'profile');

            } else {
                Response::sendAlert("error", LangManager::translate("users.toaster.error"),LangManager::translate("users.toaster.mail_pass_matching"));
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
    public function login(): void
    {
        if (UsersModel::getLoggedUser() !== -1) {
            header('Location: ' . getenv('PATH_SUBFOLDER'));
            die();
        }


        $view = new View("Users", "login");
        $view->view();
    }

    /**
     * @throws \CMW\Manager\Router\RouterException
     */
    #[Link('/login/forgot', Link::GET)]
    public function forgotPassword(): void
    {
        if (UsersModel::getLoggedUser() !== -1) {
            header('Location: ' . getenv('PATH_SUBFOLDER'));
            die();
        }

        $view = new View("Users", "forgot_password");
        $view->view();
    }


    #[Link('/login/forgot', Link::POST)]
    public function forgotPasswordPost(): void
    {
        $mail = filter_input(INPUT_POST, "mail");

        //We check if this email exist
        if($this->userModel->checkEmail($mail) <= 0) {
            //TODO toaster with error
            die();
        }

        //We send a verification link for this mail
        $this->userModel->resetPassword($mail);
        header("Location: /login");
    }

    /**
     * @throws \CMW\Manager\Router\RouterException
     */
    #[Link('/register', Link::GET)]
    public function register(): void
    {
        if (UsersModel::getLoggedUser() !== -1) {
            header('Location: ' . getenv('PATH_SUBFOLDER'));
            die();
        }

        $view = new View("Users", "register");
        $view->view();
    }

    #[Link('/register', Link::POST)]
    public function registerPost(): void
    {
        if(SecurityController::checkCaptcha()) {
        if ($this->userModel->checkPseudo(filter_input(INPUT_POST, "register_pseudo")) > 0) {
            Response::sendAlert("error", LangManager::translate("users.toaster.error"),LangManager::translate("users.toaster.used_pseudo"));
            header('Location: register');
        } else if ($this->userModel->checkEmail(filter_input(INPUT_POST, "register_email")) > 0) {
            Response::sendAlert("error", LangManager::translate("users.toaster.error"),LangManager::translate("users.toaster.used_mail"));
            header('Location: register');
        } else {

            [$mail, $pseudo, $password, $passwordVerify] = Utils::filterInput("register_email", "register_pseudo", "register_password", "register_password_verify");

            if (!is_null($password) && $password !== $passwordVerify) {
                Response::sendAlert("error", LangManager::translate("users.toaster.error"),LangManager::translate("users.toaster.not_same_pass"));
                header('Location: register');
            }

            $userEntity = $this->userModel->create($mail, $pseudo, "", "", array("2"));

            $this->userModel->updatePass($userEntity?->getId(), password_hash($password, PASSWORD_BCRYPT));


            /* Connection */

            $infos = array(
                "email" => filter_input(INPUT_POST, "register_email"),
                "password" => filter_input(INPUT_POST, "register_password")
            );

            $cookie = 1;

            $userId = UsersModel::logIn($infos, $cookie);
            if ($userId > 0 && $userId !== "ERROR") {
                $this->userModel->updateLoggedTime($userId);
                header('Location: ' . getenv('PATH_SUBFOLDER') . 'profile');


                Response::sendAlert("success", LangManager::translate("users.toaster.success"),LangManager::translate("users.toaster.welcome"));

            }

        }
        } else {
            //TODO Toaster invalid captcha
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }

    }

    /**
     * @throws \CMW\Manager\Router\RouterException
     */
    #[Link('/profile', Link::GET)]
    public function publicProfile(): void
    {
        if (!$this->userSettingsEntity->isProfilePageEnabled() && UsersModel::getLoggedUser() === -1){
            Redirect::redirect('login');
            return;
        }

        if (!$this->userSettingsEntity->isProfilePageEnabled()){
            Redirect::redirectToHome();
            return;
        }

        if (UsersModel::getLoggedUser() === -1) {
            Redirect::redirect('login');
            return;
        }

        $user = UsersModel::getCurrentUser();

        if ($this->userSettingsEntity->getProfilePageStatus() === 1) {
            Redirect::redirect("profile/", ['pseudo' => $user?->getPseudo()]);
        }

        $view = new View('Users', 'profile');
        $view->addVariableList(["user" => $user]);
        $view->view();
    }

    #[Link('/profile', Link::POST)]
    public function publicProfilePost(): void
    {
        if (!$this->userSettingsEntity->isProfilePageEnabled()){
            Redirect::redirectToHome();
            return;
        }

        if (UsersModel::getLoggedUser() === -1) {
            Redirect::redirectToHome();
            return;
        }

        $image = $_FILES['pictureProfile'];

        try {
            $this->userPictureModel->uploadImage($_SESSION['cmwUserId'], $image);
        } catch (Exception $e) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError") . " => $e");
        }

        header('Location: ' . getenv('PATH_SUBFOLDER') . 'profile');
    }

    #[Link('/profile/:pseudo', Link::GET, ['pseudo' => '.*?'])]
    public function publicProfileWithPseudo(Request $request, string $pseudo): void
    {
        if (!$this->userSettingsEntity->isProfilePageEnabled() && UsersModel::getLoggedUser() === -1){
            Redirect::redirect('login');
            return;
        }

        if (!$this->userSettingsEntity->isProfilePageEnabled()){
            Redirect::redirectToHome();
            return;
        }

        if ($this->userSettingsEntity->getProfilePageStatus() === 0) {
            Redirect::redirect("profile");
        }

        $user = $this->userModel->getUserWithPseudo($pseudo);

        if (is_null($user)) {
            Redirect::errorPage(404);
        }

        $view = new View('Users', 'profile');
        $view->addVariableList(["user" => $user]);
        $view->view();
    }

    #[Link("/profile/delete/:id", Link::GET, ["id" => "[0-9]+"])]
    public function publicProfileDelete(Request $request, int $id): void
    {
        //Check if this is the current user account
        if ($_SESSION['cmwUserId'] !== $id) {
            //TODO ERROR MANAGEMENT (MESSAGE TO TELL THE USER CAN'T DELETE THIS ACCOUNT)
            header('Location: ' . getenv('PATH_SUBFOLDER') . 'profile');
            return;
        }

        UsersModel::logOut();
        $this->userModel->delete($id);

        header('Location: ' . getenv('PATH_SUBFOLDER'));
    }

    #[Link('/logout', Link::GET)]
    public function logOut(): void
    {
        UsersModel::logOut();
        header('Location: ' . getenv('PATH_SUBFOLDER'));
    }

    #[Link('/profile/update', Link::POST)]
    public function publicProfileUpdate(): void
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

        $this->userModel->update($userId, $mail, $username, $firstname, $lastname, $rolesId);


        [$pass, $passVerif] = Utils::filterInput("password", "passwordVerif");

        if (!is_null($pass)) {
            if ($pass === $passVerif) {
                $this->userModel->updatePass($userId, password_hash($pass, PASSWORD_BCRYPT));
            } else {
                //Todo Try to edit that
                Response::sendAlert("error", LangManager::translate("users.toaster.error"),"Je sais pas ?");
            }
        }

        header('Location: ' . getenv('PATH_SUBFOLDER') . 'profile');
    }

}
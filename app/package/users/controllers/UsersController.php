<?php

namespace CMW\Controller\Users;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Core\SecurityController;
use CMW\Entity\Users\UserEntity;
use CMW\Model\Core\CoreModel;
use CMW\Model\Users\PermissionsModel;
use CMW\Model\Users\RolesModel;
use CMW\Model\Users\UserPictureModel;
use CMW\Model\Users\UsersModel;
use CMW\Router\Link;
use CMW\Utils\Utils;
use CMW\Utils\View;
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

    public function __construct($theme_path = null)
    {
        parent::__construct($theme_path);
        $this->userModel = new UsersModel();
        $this->roleModel = new RolesModel();
        $this->userPictureModel = new UserPictureModel();
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
            ->addStyle("admin/resources/vendors/simple-datatables/style.css","admin/resources/assets/css/pages/simple-datatables.css")
            ->addScriptBefore("app/package/users/views/assets/js/edit.js")
            ->addScriptAfter("admin/resources/vendors/simple-datatables/umd/simple-datatables.js",
                "admin/resources/assets/js/pages/simple-datatables.js")
            ->view();
    }

    public static function redirectIfNotHavePermissions(string ...$permCode): void
    {
        if (!(self::hasPermission(...$permCode))) {
            self::redirectToHome();
        }
    }

    #[Link("/edit/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/users")]
    public function adminUsersEdit(int $id): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

        $userEntity = $this->userModel->getUserById($id);

        $roles = $this->roleModel->getRoles();

        View::createAdminView("users", "user")->addVariableList(array(
            "user" => $userEntity,
            "roles" => $roles
        ))
            ->view();
    }

    #[Link("/getUser/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/users")]
    public function admingetUser(int $id): void
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
            "username" => $user?->getUsername(),
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


    #[Link("/edit/:id", Link::POST, ["id" => "[0-9]+"], "/cmw-admin/users")]
    #[NoReturn] public function adminUsersEditPost(int $id): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

        [$mail, $username, $firstname, $lastname] = Utils::filterInput("email", "pseudo", "name", "lastname");
        $this->userModel->update($id, $mail, $username, $firstname, $lastname, $_POST['roles']);

        //Todo Try to edit that
        $_SESSION['toaster'][0]['title'] = "USERS_TOASTER_TITLE";
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = "USERS_EDIT_TOASTER_SUCCESS";

        [$pass, $passVerif] = Utils::filterInput("pass", "passVerif");

        if (!is_null($pass)) {
            if ($pass === $passVerif) {
                $this->userModel->updatePass($id, password_hash($pass, PASSWORD_BCRYPT));
            } else {
                //Todo Try to edit that
                $_SESSION['toaster'][1]['title'] = "USERS_TOASTER_TITLE_ERROR";
                $_SESSION['toaster'][1]['type'] = "bg-danger";
                $_SESSION['toaster'][1]['body'] = "USERS_EDIT_TOASTER_PASS_ERROR";

            }

        }

        header("location: " . $_SERVER['HTTP_REFERER']);
    }

    #[Link("/add", Link::GET, [], "/cmw-admin/users")]
    public function adminUsersAdd(): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

        $roles = $this->roleModel->getRoles();

        View::createAdminView("users", "add")->addVariable("roles", $roles)
            ->view();
    }


    //Useless ?
    public function rolesTest(): void
    {

        $permissions = new PermissionsController();
        $permModel = new PermissionsModel();

        $view = View::createAdminView("users", "test")->addVariableList(array(
            "perms" => $permissions,
            "pmodel" => $permModel
        ));
        $view->view();

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
    #[NoReturn] public function adminUserState(int $id, int $state): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

        if (UsersModel::getLoggedUser() === $id) {
            $_SESSION['toaster'][0]['title'] = "USERS_TOASTER_TITLE_ERROR";
            $_SESSION['toaster'][0]['type'] = "bg-danger";
            $_SESSION['toaster'][0]['body'] = "USERS_STATE_TOASTER_ERROR";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            die();
        }

        $state = ($state) ? 0 : 1;

        $this->userModel->changeState($id, $state);

        $_SESSION['toaster'][0]['title'] = "USERS_TOASTER_TITLE";
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = "USERS_STATE_TOASTER_SUCCESS";

        header("location: " . $_SERVER['HTTP_REFERER']);
    }

    #[Link("/delete/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/users")]
    #[NoReturn] public function adminUsersDelete(int $id): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.delete");

        if (UsersModel::getLoggedUser() === $id) {

            //Todo Try to remove that
            $_SESSION['toaster'][0]['title'] = "USERS_TOASTER_TITLE_ERROR";
            $_SESSION['toaster'][0]['type'] = "bg-danger";
            $_SESSION['toaster'][0]['body'] = "USERS_DELETE_TOASTER_ERROR";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            die();
        }

        $this->userModel->delete($id);

        //Todo Try to remove that
        $_SESSION['toaster'][0]['title'] = "USERS_TOASTER_TITLE";
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = "USERS_DELETE_TOASTER_SUCCESS";

        header("location: ../list");
    }

    #[Link("/picture/edit/:id", Link::POST, ["id" => "[0-9]+"], "/cmw-admin/users")]
    #[NoReturn] public function adminUsersEditPicturePost(int $id): void
    {
        $image = $_FILES['profilePicture'];


        $this->userPictureModel->uploadImage($id, $image);

        header("Location: ../../edit/$id");
    }

    #[Link("/picture/reset/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/users")]
    #[NoReturn] public function adminUsersResetPicture(int $id): void
    {
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
                $_SESSION['toaster'][0]['title'] = "Désolé";
                $_SESSION['toaster'][0]['body'] = "Cette combinaison email/mot de passe est erronée";
                $_SESSION['toaster'][0]['type'] = "bg-danger";
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            }
        } else {
            //TODO Toaster invalid captcha
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
    }

    /**
     * @throws \CMW\Router\RouterException
     */
    #[Link('/login', Link::GET)]
    public function login(): void
    {
        if (UsersModel::getLoggedUser() !== -1) {
            header('Location: ' . getenv('PATH_SUBFOLDER'));
            die();
        }


        $view = new View("users", "login");
        $view->view();
    }

    /**
     * @throws \CMW\Router\RouterException
     */
    #[Link('/login/forgot', Link::GET)]
    public function forgotPassword(): void
    {
        if (UsersModel::getLoggedUser() !== -1) {
            header('Location: ' . getenv('PATH_SUBFOLDER'));
            die();
        }

        $view = new View("users", "forgot_password");
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
     * @throws \CMW\Router\RouterException
     */
    #[Link('/register', Link::GET)]
    public function register(): void
    {
        if (UsersModel::getLoggedUser() !== -1) {
            header('Location: ' . getenv('PATH_SUBFOLDER'));
            die();
        }

        $view = new View("users", "register");
        $view->view();
    }

    #[Link('/register', Link::POST)]
    public function registerPost(): void
    {
        if(SecurityController::checkCaptcha()) {
        if ($this->userModel->checkPseudo(filter_input(INPUT_POST, "register_pseudo")) > 0) {
            $_SESSION['toaster'][0]['title'] = "Désolé";
            $_SESSION['toaster'][0]['body'] = "Ce pseudo est déjà pris.";
            $_SESSION['toaster'][0]['type'] = "bg-danger";
            header('Location: register');
        } else if ($this->userModel->checkEmail(filter_input(INPUT_POST, "register_email")) > 0) {
            $_SESSION['toaster'][0]['title'] = "Désolé";
            $_SESSION['toaster'][0]['body'] = "Cette e-mail est déjà prise.";
            $_SESSION['toaster'][0]['type'] = "bg-danger";
            header('Location: register');
        } else {

            [$mail, $pseudo, $password, $passwordVerify] = Utils::filterInput("register_email", "register_pseudo", "register_password", "register_password_verify");

            if (!is_null($password) && $password !== $passwordVerify) {
                $_SESSION['toaster'][0]['title'] = "Désolé";
                $_SESSION['toaster'][0]['body'] = "Mots de passes non identiques";
                $_SESSION['toaster'][0]['type'] = "bg-danger";
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


                $_SESSION['toaster'][0]['title'] = "Inscription réussie";
                $_SESSION['toaster'][0]['type'] = "bg-success";
                $_SESSION['toaster'][0]['body'] = "Bienvenue sur " . CoreModel::getOptionValue("name");

            }

        }
        } else {
            //TODO Toaster invalid captcha
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }

    }

    /**
     * @throws \CMW\Router\RouterException
     */
    #[Link('/profile', Link::GET)]
    public function publicProfile(): void
    {

        if (UsersModel::getLoggedUser() === -1) {
            header('Location: ' . getenv('PATH_SUBFOLDER'));
            die();
        }

        $user = (new usersModel())->getUserById($_SESSION['cmwUserId']);

        $view = new View('users', 'profile');
        $view->addVariableList(["user" => $user]);
        $view->view();
    }

    #[Link('/profile', Link::POST)]
    public function publicProfilePost(): void
    {
        $image = $_FILES['pictureProfile'];

        $this->userPictureModel->uploadImage($_SESSION['cmwUserId'], $image);

        header('Location: ' . getenv('PATH_SUBFOLDER') . 'profile');
    }

    #[Link("/profile/delete/:id", Link::GET, ["id" => "[0-9]+"])]
    public function publicProfileDelete(int $id): void
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
                $_SESSION['toaster'][1]['title'] = "USERS_TOASTER_TITLE_ERROR";
                $_SESSION['toaster'][1]['type'] = "bg-danger";
                $_SESSION['toaster'][1]['body'] = "USERS_EDIT_TOASTER_PASS_ERROR";
            }
        }

        header('Location: ' . getenv('PATH_SUBFOLDER') . 'profile');
    }

}
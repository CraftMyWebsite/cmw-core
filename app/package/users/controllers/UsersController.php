<?php

namespace CMW\Controller\Users;

use CMW\Controller\CoreController;
use CMW\Controller\Menus\MenusController;
use CMW\Model\CoreModel;
use CMW\Model\Roles\RolesModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;

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

    public function __construct($theme_path = null)
    {
        parent::__construct($theme_path);
        $this->userModel = new UsersModel();
        $this->roleModel = new RolesModel();
    }

    public function adminDashboard(): void
    {
        if (isset($_SESSION['cmwUserId']) && UsersModel::getLoggedUser() !== -1) {
            //TODO WARNING : CHECK IF IS AN ADMIN USER!!!
            header('Location: ' . getenv('PATH_SUBFOLDER') . "cmw-admin/dashboard");
        } else {
            header('Location: ' . getenv('PATH_SUBFOLDER') . "login");
        }
    }

    public static function isAdminLogged(): void
    {
        if (UsersModel::getLoggedUser() !== -1) {
            $user = new UsersModel();
            $userEntity = $user->getUserById($_SESSION['cmwUserId']);

            if (!$user->hasPermission($userEntity?->getId(), "*")
                && !$user->hasPermission($userEntity?->getId(), "core.dashboard")) {
                header('Location: ' . getenv('PATH_SUBFOLDER'));
                exit();
            }
        } else {
            header('Location: ' . getenv('PATH_SUBFOLDER'));
            exit();
        }
    }

    public static function isAdminLoggedBool(): bool
    {
        if (UsersModel::getLoggedUser() !== -1) {
            $user = new UsersModel();
            $userEntity = $user->getUserById($_SESSION['cmwUserId']);

            if (!$user->hasPermission($userEntity?->getId(), "*") && !$user->hasPermission($userEntity?->getId(), "core.dashboard")) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    public function adminUsersList(): void
    {
        self::isUserHasPermission("users.show");
        $userList = $this->userModel->getUsers();

        view('users', 'list.admin', ["userList" => $userList], 'admin', []);
    }

    public function adminUsersEdit($id): void
    {
        self::isUserHasPermission("users.edit");

        $userEntity = $this->userModel->getUserById($id);

        $roles = $this->roleModel->fetchAll();

        view('users', 'user.admin', ["user" => $userEntity, "roles" => $roles], 'admin', []);
    }

    #[NoReturn] public function adminUsersEditPost($id): void
    {
        self::isUserHasPermission("users.edit");

        $mail = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
        $username = filter_input(INPUT_POST, "pseudo", FILTER_SANITIZE_STRING);
        $firstname = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
        $lastname = filter_input(INPUT_POST, "lastname", FILTER_SANITIZE_STRING);
        $this->userModel->updateUser($id, $mail, $username, $firstname, $lastname, $_POST['roles']);

        //Todo Try to edit that
        $_SESSION['toaster'][0]['title'] = USERS_TOASTER_TITLE;
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = USERS_EDIT_TOASTER_SUCCESS;

        if (!empty(filter_input(INPUT_POST, "pass", FILTER_SANITIZE_STRING))) {
            if (filter_input(INPUT_POST, "pass", FILTER_SANITIZE_STRING) === filter_input(INPUT_POST, "pass_verif", FILTER_SANITIZE_STRING)) {
                $this->userModel->updatePass($id, password_hash(filter_input(INPUT_POST, "pass", FILTER_SANITIZE_STRING), PASSWORD_BCRYPT));
            } else {
                //Todo Try to edit that
                $_SESSION['toaster'][1]['title'] = USERS_TOASTER_TITLE_ERROR;
                $_SESSION['toaster'][1]['type'] = "bg-danger";
                $_SESSION['toaster'][1]['body'] = USERS_EDIT_TOASTER_PASS_ERROR;

            }

        }

        header("location: ../edit/" . $id);
        die();
    }

    public function adminUsersAdd(): void
    {
        self::isUserHasPermission("users.add");

        $roles = $this->roleModel->fetchAll();

        view('users', 'add.admin', ["roles" => $roles], 'admin', []);
    }

    public function adminUsersAddPost(): void
    {
        self::isUserHasPermission("users.add");

        $mail = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
        $username = filter_input(INPUT_POST, "pseudo", FILTER_SANITIZE_STRING);
        $firstname = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
        $lastname = filter_input(INPUT_POST, "lastname", FILTER_SANITIZE_STRING);
        $userEntity = $this->userModel->createUser($mail, $username, $firstname, $lastname, $_POST['roles']);

        $this->userModel->updatePass($userEntity?->getId(), password_hash(filter_input(INPUT_POST, "pass", FILTER_SANITIZE_STRING), PASSWORD_BCRYPT));

        header("location: ../users/list");
    }

    #[NoReturn] public function adminUserState(): void
    {
        self::isUserHasPermission("users.edit");

        if (UsersModel::getLoggedUser() == filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)) {
            $_SESSION['toaster'][0]['title'] = USERS_TOASTER_TITLE_ERROR;
            $_SESSION['toaster'][0]['type'] = "bg-danger";
            $_SESSION['toaster'][0]['body'] = USERS_STATE_TOASTER_ERROR;
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            die();
        }

        $id = filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);
        $state = (filter_input(INPUT_POST, "actual_state", FILTER_SANITIZE_NUMBER_INT)) ? 0 : 1;

        $this->userModel->changeState($id, $state);

        $_SESSION['toaster'][0]['title'] = USERS_TOASTER_TITLE;
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = USERS_STATE_TOASTER_SUCCESS;

        header("location: " . $_SERVER['HTTP_REFERER']);
        die();
    }

    #[NoReturn] public function adminUsersDelete(): void
    {
        self::isUserHasPermission("users.delete");

        if (UsersModel::getLoggedUser() == filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT)) {

            //Todo Try to remove that
            $_SESSION['toaster'][0]['title'] = USERS_TOASTER_TITLE_ERROR;
            $_SESSION['toaster'][0]['type'] = "bg-danger";
            $_SESSION['toaster'][0]['body'] = USERS_DELETE_TOASTER_ERROR;
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            die();
        }

        $id = filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);
        $this->userModel->delete($id);

        //Todo Try to remove that
        $_SESSION['toaster'][0]['title'] = USERS_TOASTER_TITLE;
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = USERS_DELETE_TOASTER_SUCCESS;

        header("location: ../users/list");
        die();
    }

    /*
        Manage user with permissions (role permissions)
    */
    public static function isUserHasPermission(string $permCode): void
    {
        if (UsersModel::getLoggedUser() !== -1) {
            $user = new UsersModel();

            if (!self::isAdminLoggedBool() || $user->hasPermission($_SESSION['cmwUserId'], $permCode) < 0) {
                header('Location: ' . getenv('PATH_SUBFOLDER'));
                exit();
            }
        } else {
            header('Location: ' . getenv('PATH_SUBFOLDER'));
            exit();
        }
    }


    // PUBLIC SECTION


    public function login(): void
    {
        if (UsersModel::getLoggedUser() !== -1) {
            header('Location: ' . getenv('PATH_SUBFOLDER'));
        } else {
            $core = new CoreController();
            $menu = new MenusController();

            view('users', 'login', ["core" => $core, "menu" => $menu], 'public', []);
        }
    }

    public function loginPost(): void
    {
        $infos = array(
            "email" => filter_input(INPUT_POST, "login_email", FILTER_SANITIZE_EMAIL),
            "password" => filter_input(INPUT_POST, "login_password", FILTER_SANITIZE_STRING)
        );
        $cookie = 0;
        if (isset($_POST['login_keep_connect']) && $_POST['login_keep_connect']) {
            $cookie = 1;
        }
        $userId = UsersModel::logIn($infos, $cookie);
        if ($userId > 0 && $userId !== "ERROR") {
            $this->userModel->updateLoggedTime($userId);
            header('Location: ' . getenv('PATH_SUBFOLDER') . 'cmw-admin/dashboard');

        } else {
            $_SESSION['toaster'][0]['title'] = "Désolé";
            $_SESSION['toaster'][0]['body'] = "Cette combinaison email/mot de passe est erronée";
            $_SESSION['toaster'][0]['type'] = "bg-danger";
            header('Location: ' . $_SERVER['HTTP_REFERER']);

        }
    }

    public function logOut(): void
    {
        UsersModel::logOut();
        header('Location: ' . getenv('PATH_SUBFOLDER'));
    }
}
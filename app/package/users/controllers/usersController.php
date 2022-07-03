<?php

namespace CMW\Controller\Users;

use CMW\Controller\coreController;
use CMW\Controller\Menus\menusController;
use CMW\Model\coreModel;
use CMW\Model\Roles\rolesModel;
use CMW\Model\Users\usersModel;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @usersController
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class usersController extends coreController
{
    private usersModel $userModel;
    private rolesModel $roleModel;

    public function __construct($theme_path = null)
    {
        parent::__construct($theme_path);
        $this->userModel = new usersModel();
        $this->roleModel = new rolesModel();
    }

    public function adminDashboard(): void
    {
        if (isset($_SESSION['cmwUserId']) && usersModel::getLoggedUser() !== -1) {
            header('Location: ' . getenv('PATH_SUBFOLDER') . "cmw-admin");
        } else {
            header('Location: ' . getenv('PATH_SUBFOLDER') . "login");
        }
    }

    public static function isAdminLogged(): void
    {
        if (usersModel::getLoggedUser() !== -1) {
            $user = new usersModel();
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
        if (usersModel::getLoggedUser() !== -1) {
            $user = new usersModel();
            $userEntity = $user->getUserById($_SESSION['cmwUserId']);

            if (!$user->hasPermission($userEntity?->getId(), "*") && !$user->hasPermission($userEntity?->getId(), "core.dashboard")) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    public function login(): void
    {
        if (usersModel::getLoggedUser() !== -1) {
            header('Location: ' . getenv('PATH_SUBFOLDER'));
        } else {
            $core = new coreController();
            $menu = new menusController();

            view('users', 'login', ["core" => $core, "menu" => $menu], 'public');
        }
    }

    public function loginPost(): void
    {
        $infos = array(
            "email" => filter_input(INPUT_POST, "login_email"),
            "password" => filter_input(INPUT_POST, "login_password")
        );
        $cookie = 0;
        if (isset($_POST['login_keep_connect']) && $_POST['login_keep_connect']) {
            $cookie = 1;
        }
        $userId = usersModel::logIn($infos, $cookie);
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

    public function lLogOut(): void
    {
        usersModel::logOut();
        header('Location: ' . getenv('PATH_SUBFOLDER'));
    }

    public function adminUsersList(): void
    {
        self::isUserHasPermission("users.show");
        $userList = $this->userModel->getUsers();

        view('users', 'list.admin', ["userList" => $userList], 'admin');
    }

    public function adminUsersEdit($id): void
    {
        self::isUserHasPermission("users.edit");

        $userEntity = $this->userModel->getUserById($id);

        $roles = $this->roleModel->fetchAll();

        view('users', 'user.admin', ["user" => $userEntity, "roles" => $roles], 'admin');
    }

    #[NoReturn] public function adminUsersEditPost($id): void
    {
        self::isUserHasPermission("users.edit");

        $mail = filter_input(INPUT_POST, "email");
        $username = filter_input(INPUT_POST, "pseudo");
        $firstname = filter_input(INPUT_POST, "name");
        $lastname = filter_input(INPUT_POST, "lastname");
        $this->userModel->updateUser($id, $mail, $username, $firstname, $lastname, $_POST['roles']);

        //Todo Try to edit that
        $_SESSION['toaster'][0]['title'] = USERS_TOASTER_TITLE;
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = USERS_EDIT_TOASTER_SUCCESS;

        if (!empty(filter_input(INPUT_POST, "pass"))) {
            if (filter_input(INPUT_POST, "pass") === filter_input(INPUT_POST, "pass_verif")) {
                $this->userModel->updatePass($id, password_hash(filter_input(INPUT_POST, "pass"), PASSWORD_BCRYPT));
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

        view('users', 'add.admin', ["roles" => $roles], 'admin');
    }

    public function adminUsersAddPost(): void
    {
        self::isUserHasPermission("users.add");

        $mail = filter_input(INPUT_POST, "email");
        $username = filter_input(INPUT_POST, "pseudo");
        $firstname = filter_input(INPUT_POST, "name");
        $lastname = filter_input(INPUT_POST, "lastname");
        $userEntity = $this->userModel->createUser($mail, $username, $firstname, $lastname, $_POST['roles']);

        $this->userModel->updatePass($userEntity?->getId(), password_hash(filter_input(INPUT_POST, "pass"), PASSWORD_BCRYPT));

        header("location: ../users/list");
    }

    #[NoReturn] public function adminUserState(): void
    {
        self::isUserHasPermission("users.edit");

        if (usersModel::getLoggedUser() == filter_input(INPUT_POST, "id")) {
            $_SESSION['toaster'][0]['title'] = USERS_TOASTER_TITLE_ERROR;
            $_SESSION['toaster'][0]['type'] = "bg-danger";
            $_SESSION['toaster'][0]['body'] = USERS_STATE_TOASTER_ERROR;
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            die();
        }

        $id = filter_input(INPUT_POST, "id");
        $state = (filter_input(INPUT_POST, "actual_state")) ? 0 : 1;

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

        if (usersModel::getLoggedUser() == filter_input(INPUT_POST, "id")) {

            //Todo Try to remove that
            $_SESSION['toaster'][0]['title'] = USERS_TOASTER_TITLE_ERROR;
            $_SESSION['toaster'][0]['type'] = "bg-danger";
            $_SESSION['toaster'][0]['body'] = USERS_DELETE_TOASTER_ERROR;
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            die();
        }

        $id = filter_input(INPUT_POST, "id");
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
        if (usersModel::getLoggedUser() !== -1) {
            $user = new usersModel();

            if (!self::isAdminLoggedBool() || $user->hasPermission($_SESSION['cmwUserId'], $permCode) < 0) {
                header('Location: ' . getenv('PATH_SUBFOLDER'));
                exit();
            }
        } else {
            header('Location: ' . getenv('PATH_SUBFOLDER'));
            exit();
        }
    }
}
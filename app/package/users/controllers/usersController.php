<?php

namespace CMW\Controller\Users;

use CMW\Controller\coreController;
use CMW\Model\Roles\rolesModel;
use CMW\Model\Users\usersModel;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @usersController
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class usersController extends coreController
{
    public static function isAdminLogged(): void
    {
        if (usersModel::getLoggedUser() !== -1) {
            $user = new usersModel();
            $user->fetch($_SESSION['cmwUserId']);

            if (!$user->hasPermission($_SESSION['cmwUserId'], "*") && !$user->hasPermission($_SESSION['cmwUserId'], "core.dashboard")) {
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
            $user->fetch($_SESSION['cmwUserId']);

            if (!$user->hasPermission($_SESSION['cmwUserId'], "*") && !$user->hasPermission($_SESSION['cmwUserId'], "core.dashboard")) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    public function adminLogin(): void
    {
        if (usersModel::getLoggedUser() !== -1) {
            header('Location: ' . getenv('PATH_SUBFOLDER') . 'cmw-admin/dashboard');
        } else {
            view('users', 'login.admin', [], 'admin', 1);
        }
    }

    public function adminLoginPost(): void
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
            $user = new usersModel();
            $user->userId = $userId;
            $user->updateLoggedTime();
            header('Location: ' . getenv('PATH_SUBFOLDER') . 'cmw-admin/dashboard');
        } else {
            $_SESSION['toaster'][0]['title'] = "Désolé";
            $_SESSION['toaster'][0]['body'] = "Cette combinaison email/mot de passe est erronée";
            $_SESSION['toaster'][0]['type'] = "bg-danger";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
    }

    public function adminLogOut(): void
    {
        usersModel::logout();
        header('Location: ' . getenv('PATH_SUBFOLDER') . 'cmw-admin');
    }

    public function adminUsersList(): void
    {
        self::isUserHasPermission("users.show");

        $usersModel = new usersModel();
        $userList = $usersModel->fetchAll();

        view('users', 'list.admin', ["userList" => $userList], 'admin');
    }

    public function adminUsersEdit($id): void
    {
        self::isUserHasPermission("users.edit");

        $user = new usersModel();
        $user->fetch($id);

        $roles = new rolesModel();
        $roles = $roles->fetchAll();

        view('users', 'user.admin', ["user" => $user, "roles" => $roles], 'admin');
    }

    #[NoReturn] public function adminUsersEditPost($id): void
    {
        self::isUserHasPermission("users.edit");

        $user = new usersModel();
        $user->userId = $id;
        $user->userEmail = filter_input(INPUT_POST, "email");
        $user->userPseudo = filter_input(INPUT_POST, "pseudo");
        $user->userFirstname = filter_input(INPUT_POST, "name");
        $user->userLastname = filter_input(INPUT_POST, "lastname");
        $user->update($_POST['roles']);

        //Todo Try to edit that
        $_SESSION['toaster'][0]['title'] = USERS_TOASTER_TITLE;
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = USERS_EDIT_TOASTER_SUCCESS;

        if (!empty(filter_input(INPUT_POST, "pass"))) {
            if (filter_input(INPUT_POST, "pass") === filter_input(INPUT_POST, "pass_verif")) {
                $user->setPassword(password_hash(filter_input(INPUT_POST, "pass"), PASSWORD_BCRYPT));
                $user->updatePass();

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

        $roles = new rolesModel();
        $roles = $roles->fetchAll();

        view('users', 'add.admin', ["roles" => $roles], 'admin');
    }

    public function adminUsersAddPost(): void
    {
        self::isUserHasPermission("users.add");

        $user = new usersModel();
        $user->userEmail = filter_input(INPUT_POST, "email");
        $user->userPseudo = filter_input(INPUT_POST, "pseudo");
        $user->userFirstname = filter_input(INPUT_POST, "name");
        $user->userLastname = filter_input(INPUT_POST, "lastname");
        $user->create($_POST['roles']);

        $user->setPassword(password_hash(filter_input(INPUT_POST, "pass"), PASSWORD_BCRYPT));
        $user->updatePass();

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

        $state = (filter_input(INPUT_POST, "actual_state")) ? 0 : 1;

        $user = new usersModel();
        $user->userId = filter_input(INPUT_POST, "id");
        $user->userState = $state;
        $user->changeState();

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

        $user = new usersModel();
        $user->userId = filter_input(INPUT_POST, "id");
        $user->delete();

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
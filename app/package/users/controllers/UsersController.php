<?php

namespace CMW\Controller\Users;

use CMW\Controller\CoreController;
use CMW\Controller\Menus\MenusController;

use CMW\Controller\Permissions\PermissionsController;
use CMW\Entity\Users\UserEntity;

use CMW\Entity\Users\UserEntity;
use CMW\Model\CoreModel;
use CMW\Model\Permissions\PermissionsModel;
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

    private static function getSessionUser(): ?UserEntity
    {
        if (is_null($_SESSION['cmwUserId'])) {
            return null;
        }

        return (new UsersModel())->getUserById($_SESSION['cmwUserId']);
    }

    private static function hasPermission(string ...$permissions): bool
    {
        return UsersModel::hasPermission(self::getSessionUser(), ...$permissions);
    }

    public function adminDashboard(): void
    {
        header("Location" . getenv("PATH_SUBFOLDER") . ((self::isAdminLogged()) ? "cmw-admin/dashboard" : "login"));
    }

    public static function isAdminLogged(): bool
    {
        return UsersModel::hasPermission(self::getSessionUser(), "core.dashboard");
    }

    public static function redirectIfNotHavePermissions(string ...$permCode): void
    {
        if (!(self::hasPermission(...$permCode))) {
            self::redirectToHome();
        }
    }

    public function adminUsersList(): void
    {

        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

        $userList = $this->userModel->getUsers();

        view('users', 'list.admin', ["userList" => $userList], 'admin', []);
    }

    public function adminUsersEdit($id): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

        $userEntity = $this->userModel->getUserById($id);

        $roles = $this->roleModel->getRoles();

        view('users', 'user.admin', ["user" => $userEntity, "roles" => $roles], 'admin', []);
    }

    #[NoReturn] public function adminUsersEditPost($id): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

        [$mail, $username, $firstname, $lastname] = Utils::filterInput("email", "pseudo", "name", "lastname");
        $this->userModel->update($id, $mail, $username, $firstname, $lastname, $_POST['roles']);

        //Todo Try to edit that
        $_SESSION['toaster'][0]['title'] = USERS_TOASTER_TITLE;
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = USERS_EDIT_TOASTER_SUCCESS;

        [$pass, $passVerif] = Utils::filterInput("pass", "passVerif");

        if (!is_null($pass)) {
            if ($pass === $passVerif) {
                $this->userModel->updatePass($id, password_hash($pass, PASSWORD_BCRYPT));
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
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

        $roles = $this->roleModel->getRoles();

        view('users', 'add.admin', ["roles" => $roles], 'admin', []);
    }


    public function rolesTest(): void {

        $permissions = new PermissionsController();
        $permModel = new PermissionsModel();

        view('users', 'test.admin', ["perms" => $permissions, "pmodel" => $permModel], 'admin');
    }


    public function adminUsersAddPost(): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.add");

        [$mail, $username, $firstname, $lastname] = Utils::filterInput("email", "pseudo", "name", "lastname");

        $userEntity = $this->userModel->create($mail, $username, $firstname, $lastname, $_POST['roles']);

        $this->userModel->updatePass($userEntity?->getId(), password_hash(filter_input(INPUT_POST, "pass", FILTER_SANITIZE_STRING), PASSWORD_BCRYPT));

        header("location: ../users/list");
    }

    #[NoReturn] public function adminUserState(): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

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
        self::redirectIfNotHavePermissions("core.dashboard", "users.delete");

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
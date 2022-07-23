<?php

namespace CMW\Controller\Users;

use CMW\Controller\CoreController;
use CMW\Controller\Menus\MenusController;

use CMW\Controller\Permissions\PermissionsController;
use CMW\Entity\Users\UserEntity;

use CMW\Model\Permissions\PermissionsModel;
use CMW\Model\Roles\RolesModel;
use CMW\Model\Users\UsersModel;
use CMW\Router\Link;
use CMW\Utils\Utils;
use CMW\Utils\View;
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

    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin/users")]
    #[Link("/list", Link::GET, [], "/cmw-admin/users")]
    public function adminUsersList(): void
    {

        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

        $userList = $this->userModel->getUsers();

        View::createAdminView("users", "list")->addVariable("userList", $userList)
        ->view();
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

    #[Link("/edit/:id", Link::POST, ["id" => "[0-9]+"], "/cmw-admin/users")]
    #[NoReturn] public function adminUsersEditPost(int $id): void
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
    public function rolesTest(): void {

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

        [$mail, $username, $firstname, $lastname] = Utils::filterInput("email", "pseudo", "name", "lastname");

        $userEntity = $this->userModel->create($mail, $username, $firstname, $lastname, $_POST['roles']);

        $this->userModel->updatePass($userEntity?->getId(), password_hash(filter_input(INPUT_POST, "pass", FILTER_SANITIZE_STRING), PASSWORD_BCRYPT));

        header("location: ../users/list");
    }

    #[Link("/state/:id/:state", Link::GET, ["id" => "[0-9]+", "state" => "[0-9]+"], "/cmw-admin/users")]
    #[NoReturn] public function adminUserState(int $id, int $state): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

        if (UsersModel::getLoggedUser() == $id) {
            $_SESSION['toaster'][0]['title'] = USERS_TOASTER_TITLE_ERROR;
            $_SESSION['toaster'][0]['type'] = "bg-danger";
            $_SESSION['toaster'][0]['body'] = USERS_STATE_TOASTER_ERROR;
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            die();
        }

        $state = ($state) ? 0 : 1;

        $this->userModel->changeState($id, $state);

        $_SESSION['toaster'][0]['title'] = USERS_TOASTER_TITLE;
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = USERS_STATE_TOASTER_SUCCESS;

        header("location: " . $_SERVER['HTTP_REFERER']);
    }

    #[Link("/delete/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/users")]
    #[NoReturn] public function adminUsersDelete(int $id): void
    {
        self::redirectIfNotHavePermissions("core.dashboard", "users.delete");

        if (UsersModel::getLoggedUser() == $id) {

            //Todo Try to remove that
            $_SESSION['toaster'][0]['title'] = USERS_TOASTER_TITLE_ERROR;
            $_SESSION['toaster'][0]['type'] = "bg-danger";
            $_SESSION['toaster'][0]['body'] = USERS_DELETE_TOASTER_ERROR;
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            die();
        }

        $this->userModel->delete($id);

        //Todo Try to remove that
        $_SESSION['toaster'][0]['title'] = USERS_TOASTER_TITLE;
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = USERS_DELETE_TOASTER_SUCCESS;

        header("location: ../list");
    }


    // PUBLIC SECTION

    #[Link('/login', Link::GET)]
    public function login(): void
    {
        if (UsersModel::getLoggedUser() !== -1) {
            header('Location: ' . getenv('PATH_SUBFOLDER'));
            die();
        }

        $menu = new MenusController();

        $view = new View("users", "login");
        $view->addVariable("menu", $menu)->view();
    }

    #[Link('/login', Link::POST)]
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

    #[Link('/logout', Link::GET)]
    public function logOut(): void
    {
        UsersModel::logOut();
        header('Location: ' . getenv('PATH_SUBFOLDER'));
    }
}
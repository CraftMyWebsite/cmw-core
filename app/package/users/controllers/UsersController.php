<?php

namespace CMW\Controller\Users;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Menus\MenusController;
use CMW\Controller\Users\PermissionsController;
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

    private static function hasPermission(string ...$permissions): bool
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
    #[Link("/list", Link::GET, [], "/cmw-admin/users")]
    public function adminUsersList(): void
    {

        self::redirectIfNotHavePermissions("core.dashboard", "users.edit");

        $userList = $this->userModel->getUsers();

        View::createAdminView("users", "list")->addVariable("userList", $userList)
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

        [$mail, $username, $firstname, $lastname] = Utils::filterInput("email", "pseudo", "name", "lastname");

        $userEntity = $this->userModel->create($mail, $username, $firstname, $lastname, $_POST['roles']);

        $this->userModel->updatePass($userEntity?->getId(), password_hash(filter_input(INPUT_POST, "pass"), PASSWORD_BCRYPT));

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
            header('Location: ' . getenv('PATH_SUBFOLDER') . 'profile');

        } else {
            $_SESSION['toaster'][0]['title'] = "Désolé";
            $_SESSION['toaster'][0]['body'] = "Cette combinaison email/mot de passe est erronée";
            $_SESSION['toaster'][0]['type'] = "bg-danger";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
    }

    #[Link('/login', Link::GET)]
    public function login(): void
    {
        if (UsersModel::getLoggedUser() !== -1) {
            header('Location: ' . getenv('PATH_SUBFOLDER'));
            die();
        }

        $menu = new MenusController();

        $view = new View("users", "login");
        $view->addVariable("menu", $menu)->setAdminView()->view();
    }

    #[Link('/register', Link::GET)]
    public function register(): void
    {
        //Default controllers (important)
        $core = new coreController();
        $menu = new menusController();

        if (UsersModel::getLoggedUser() !== -1) {
            header('Location: ' . getenv('PATH_SUBFOLDER'));
            die();
        }

        $menu = new MenusController();

        $view = new View("users", "register");
        $view->addVariable("menu", $menu)->view();
    }

    #[Link('/register', Link::POST)]
    public function registerPost(): void
    {

        if ($this->userModel->checkPseudo(filter_input(INPUT_POST, "register_pseudo")) > 0) {
            $_SESSION['toaster'][0]['title'] = "Désolé";
            $_SESSION['toaster'][0]['body'] = "Ce pseudo est déjà pris.";
            $_SESSION['toaster'][0]['type'] = "bg-danger";
            header('Location: inscription');
        } else if ($this->userModel->checkEmail(filter_input(INPUT_POST, "register_email")) > 0) {
            $_SESSION['toaster'][0]['title'] = "Désolé";
            $_SESSION['toaster'][0]['body'] = "Cette e-mail est déjà prise.";
            $_SESSION['toaster'][0]['type'] = "bg-danger";
            header('Location: inscription');
        } else {

            [$mail, $password] = Utils::filterInput("register_email", "register_password");

            $userEntity = $this->userModel->create($mail, "", "", "", array("2"));

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

    }

    #[Link('/profile', Link::GET)]
    public function publicProfile(): void
    {
        //Default controllers (important)
        $core = new coreController();
        $menu = new menusController();

        $user = (new usersModel())->getUserById($_SESSION['cmwUserId']);


        if (UsersModel::getLoggedUser() == -1) {
            header('Location: ' . getenv('PATH_SUBFOLDER'));
            die();
        }
        $view = new View('users', 'profile');
        $view->addVariableList(["core" => $core, "menu" => $menu, "user" => $user]);
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
    public function publicProfileDelete(int $id)
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
    public function publicProfileUpdate()
    {
        if (!isset($_SESSION['cmwUserId'])) {
            header('Location: ' . getenv('PATH_SUBFOLDER'));
            return;
        }

        $userId = $_SESSION['cmwUserId'];

        [$mail, $username, $firstname, $lastname] = Utils::filterInput("email", "pseudo", "name", "lastname");

        $roles = UsersModel::getRoles($userId);

        $this->userModel->update($userId, $mail, $username, $firstname, $lastname, $roles);


        [$pass, $passVerif] = Utils::filterInput("password", "passwordVerif");

        if (!is_null($pass)) {
            if ($pass === $passVerif) {
                $this->userModel->updatePass($userId, password_hash($pass, PASSWORD_BCRYPT));
            } else {
                //Todo Try to edit that
                $_SESSION['toaster'][1]['title'] = USERS_TOASTER_TITLE_ERROR;
                $_SESSION['toaster'][1]['type'] = "bg-danger";
                $_SESSION['toaster'][1]['body'] = USERS_EDIT_TOASTER_PASS_ERROR;
            }
        }

        header('Location: ' . getenv('PATH_SUBFOLDER') . 'profile');
    }

}
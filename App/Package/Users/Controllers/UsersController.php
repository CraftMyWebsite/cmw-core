<?php

namespace CMW\Controller\Users;

use CMW\Controller\Core\SecurityController;
use CMW\Entity\Users\UserPictureEntity;
use CMW\Event\Users\DeleteUserAccountEvent;
use CMW\Event\Users\LogoutEvent;
use CMW\Event\Users\RegisterEvent;
use CMW\Interface\Users\IUsersProfilePicture;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Events\Emitter;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Loader\Loader;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Security\EncryptManager;
use CMW\Manager\Views\View;
use CMW\Model\Users\RolesModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;
use JsonException;

/**
 * Class: @UsersController
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 0.0.1
 */
class UsersController extends AbstractController
{
    public static function isAdminLogged(): bool
    {
        return UsersModel::hasPermission(UsersModel::getCurrentUser(), 'core.dashboard');
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

            if ($weight > $highestWeight) {
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

    /**
     * @param int|null $userId
     * @return \CMW\Entity\Users\UserPictureEntity|null
     */
    public function getUserProfilePicture(?int $userId = null): ?UserPictureEntity
    {
        if ($userId === null) {
            $user = UsersModel::getCurrentUser();

            if ($user === null) {
                return null;
            }

            $userId = $user->getId();
        }
        return $this->getHighestImplementation(IUsersProfilePicture::class)->getUserProfilePicture($userId);
    }

    #[Link(path: '/', method: Link::GET, scope: '/cmw-admin/users')]
    #[Link('/manage', Link::GET, [], '/cmw-admin/users')]
    private function adminUsersList(): void
    {
        self::redirectIfNotHavePermissions('core.dashboard', 'users.manage');

        $userList = UsersModel::getInstance()->getUsers();
        $roles = RolesModel::getInstance()->getRoles();

        View::createAdminView('Users', 'manage')
            ->addVariableList(['userList' => $userList, 'roles' => $roles])
            ->addStyle('Admin/Resources/Assets/Css/simple-datatables.css')
            ->addScriptBefore('App/Package/Users/Views/Assets/Js/edit.js')
            ->addScriptAfter('Admin/Resources/Vendors/Simple-datatables/simple-datatables.js',
                'Admin/Resources/Vendors/Simple-datatables/config-datatables.js')
            ->view();
    }

    public static function redirectIfNotHavePermissions(string ...$permCode): void
    {
        if (!(self::hasPermission(...$permCode))) {
            Redirect::redirectToHome();
        }
    }

    #[Link('/getUser/:id', Link::GET, ['id' => '[0-9]+'], '/cmw-admin/users')]
    private function adminGetUser(int $id): void
    {
        self::redirectIfNotHavePermissions('core.dashboard', 'users.manage.edit');

        $user = (UsersModel::getInstance())->getUserById($id);

        $roles = [];

        foreach ($user?->getRoles() as $role) {
            $roles[] .= $role->getName();
        }

        $data = [
            'id' => $user?->getId(),
            'mail' => $user?->getMail(),
            'username' => $user?->getPseudo(),
            'firstName' => $user?->getFirstName() ?? '',
            'lastName' => $user?->getLastName() ?? '',
            'state' => $user?->getState(),
            'lastConnection' => $user?->getLastConnection(),
            'dateCreated' => $user?->getCreated(),
            'dateUpdated' => $user?->getUpdated(),
            'pictureLink' => $user?->getUserPicture()?->getImage(),
            'pictureLastUpdate' => $user?->getUserPicture()?->getLastUpdate(),
            'userHighestRole' => $user?->getHighestRole()?->getName(),
            'roles' => $roles,
        ];

        try {
            print_r(json_encode($data, JSON_THROW_ON_ERROR));
        } catch (JsonException) {
            print ('ERROR');
        }
    }

    #[Link('/edit/:id', Link::GET, ['id' => '[0-9]+'], '/cmw-admin/users/manage')]
    private function adminUsersEdit(int $id): void
    {
        self::redirectIfNotHavePermissions('core.dashboard', 'users.manage.edit');

        $userEntity = UsersModel::getInstance()->getUserById($id);

        $roles = RolesModel::getInstance()->getRoles();

        View::createAdminView('Users', 'user')
            ->addVariableList([
                'user' => $userEntity,
                'roles' => $roles,
            ])
            ->view();
    }

    #[NoReturn] #[Link('/edit/:id', Link::POST, ['id' => '[0-9]+'], '/cmw-admin/users/manage')]
    private function adminUsersEditPost(int $id): void
    {
        self::redirectIfNotHavePermissions('core.dashboard', 'users.manage.edit');

        [$pass, $passVerif] = Utils::filterInput('pass', 'passVerif');
        [$mail, $username, $firstname, $lastname] = Utils::filterInput('email', 'pseudo', 'name', 'lastname');

        $encryptedMail = EncryptManager::encrypt($mail);

        if (!isset($_POST['pass']) || $pass === '') {
            UsersModel::getInstance()->update($id, $encryptedMail, $username, $firstname, $lastname, $_POST['roles']);
            Flash::send(Alert::SUCCESS, LangManager::translate('users.toaster.success'),
                LangManager::translate('users.toaster.edited_not_pass_change'));
        } else if ($pass === $passVerif) {
            UsersModel::getInstance()->updatePass($id, password_hash($pass, PASSWORD_BCRYPT));
            UsersModel::getInstance()->update($id, $encryptedMail, $username, $firstname, $lastname, $_POST['roles']);
            Flash::send(Alert::SUCCESS, LangManager::translate('users.toaster.success'),
                LangManager::translate('users.toaster.edited_pass_change'));
        } else {
            Flash::send(Alert::ERROR, LangManager::translate('users.toaster.error'),
                LangManager::translate('users.toaster.not_same_pass'));
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/add', Link::POST, [], '/cmw-admin/users')]
    private function adminUsersAddPost(): void
    {
        self::redirectIfNotHavePermissions('core.dashboard', 'users.manage.add');

        [$mail, $pseudo, $firstname, $lastname] = Utils::filterInput('email', 'pseudo', 'firstname', 'surname');

        $encryptedMail = EncryptManager::encrypt(mb_strtolower($mail));

        $userEntity = UsersModel::getInstance()->create($encryptedMail, $pseudo, $firstname, $lastname, $_POST['roles']);

        if ($userEntity === null) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('users.toaster.error_add'));
            Redirect::redirectPreviousRoute();
        }

        UsersModel::getInstance()->updatePass($userEntity?->getId(), password_hash(filter_input(INPUT_POST, 'password'), PASSWORD_BCRYPT));

        $userId = $userEntity->getId();

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('users.toaster.success_add', ['pseudo' => $pseudo]));

        Emitter::send(RegisterEvent::class, $userId);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/manage/state/:id/:state', Link::GET, ['id' => '[0-9]+', 'state' => '[0-9]+'], '/cmw-admin/users')]
    private function adminUserState(int $id, int $state): void
    {
        self::redirectIfNotHavePermissions('core.dashboard', 'users.manage.edit');

        if (UsersModel::getCurrentUser()?->getId() === $id) {
            Flash::send(Alert::ERROR, LangManager::translate('users.toaster.error'),
                LangManager::translate('users.toaster.impossible'));
            Redirect::redirectPreviousRoute();
        }

        $state = ($state) ? 0 : 1;

        UsersModel::getInstance()->changeState($id, $state);

        Flash::send(Alert::SUCCESS, LangManager::translate('users.toaster.success'),
            LangManager::translate('users.toaster.status'));

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/delete/:id', Link::GET, ['id' => '[0-9]+'], '/cmw-admin/users')]
    private function adminUsersDelete(int $id): void
    {
        self::redirectIfNotHavePermissions('core.dashboard', 'users.manage.delete');

        if (UsersModel::getCurrentUser()?->getId() === $id) {
            // Todo Try to remove that
            Flash::send(Alert::ERROR, LangManager::translate('users.toaster.error'),
                LangManager::translate('users.toaster.impossible_user'));
            Redirect::redirectPreviousRoute();
        }

        Emitter::send(DeleteUserAccountEvent::class, $id);

        UsersModel::getInstance()->delete($id);

        // Todo Try to remove that
        Flash::send(Alert::SUCCESS, LangManager::translate('users.toaster.success'),
            LangManager::translate('users.toaster.user_deleted'));

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/picture/edit/:id', Link::POST, ['id' => '[0-9]+'], '/cmw-admin/users/manage')]
    private function adminUsersEditPicturePost(int $id): void
    {
        self::redirectIfNotHavePermissions('core.dashboard', 'users.manage.edit');

        $image = $_FILES['profilePicture'];
        $this->getHighestImplementation(IUsersProfilePicture::class)->changeMethod($image, $id);
    }

    #[Link('/picture/reset/:id', Link::GET, ['id' => '[0-9]+'], '/cmw-admin/users/manage')]
    #[NoReturn]
    private function adminUsersResetPicture(int $id): void
    {
        self::redirectIfNotHavePermissions('core.dashboard', 'users.edit');

        $this->getHighestImplementation(IUsersProfilePicture::class)->resetPicture($id);
    }

    // PUBLIC SECTION

    /**
     * @throws \CMW\Manager\Router\RouterException
     */
    #[Link('/login/forgot', Link::GET)]
    private function forgotPassword(): void
    {
        if (self::isUserLogged()) {
            Redirect::redirectToHome();
        }

        View::createPublicView('Users', 'forgot_password')->view();
    }

    #[NoReturn] #[Link('/login/forgot', Link::POST)]
    private function forgotPasswordPost(): void
    {
        if (SecurityController::checkCaptcha()) {
            $mail = filter_input(INPUT_POST, 'mail');

            $encryptedMail = EncryptManager::encrypt($mail);

            // We check if this email exist
            if (UsersModel::getInstance()->checkEmail($encryptedMail) <= 0) {
                Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                    LangManager::translate('users.toaster.not_registered_account'));

                Redirect::redirectPreviousRoute();
            }
            // We send a verification link for this mail
            UsersModel::getInstance()->resetPassword($encryptedMail);

            Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
                LangManager::translate('users.toaster.password_reset', ['mail' => $mail]));

            if (str_starts_with($_SERVER['HTTP_REFERER'], EnvManager::getInstance()->getValue('PATH_URL') . 'cmw-admin/')) {
                Redirect::redirectPreviousRoute();
            }

            Redirect::redirect('login');
        } else {
            // TODO Toaster invalid captcha
            Redirect::redirectPreviousRoute();
        }
    }

    #[NoReturn] #[Link('/logout', Link::GET)]
    private function logOut(): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();
        Emitter::send(LogoutEvent::class, $userId);
        UsersModel::logOut();
        Redirect::redirectToHome();
    }
}

<?php

namespace CMW\Implementation\Users\Users;

use CMW\Entity\Users\UserEntity;
use CMW\Interface\Users\IUsersSession;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Log;

/**
 * Class: @UsersSessionImplementation
 * @implements: IUsersSession
 * @package Users
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/implementations
 */
class UsersSessionImplementation implements IUsersSession
{
    public function weight(): int
    {
        return 1;
    }

    public function getCurrentUser(): ?UserEntity
    {
        if (isset($_SESSION['cmwUser']) && $_SESSION['cmwUser'] instanceof UserEntity) {
            return $_SESSION['cmwUser'];
        }

        if (isset($_COOKIE['cmw_cookies_user_id']) && filter_var($_COOKIE['cmw_cookies_user_id'], FILTER_VALIDATE_INT)) {
            return UsersModel::getInstance()->getUserById($_COOKIE['cmw_cookies_user_id']);
        }

        return null;
    }

    public function logOut(): void
    {
        $_SESSION = [];
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']);
        session_destroy();

        setcookie('cmw_cookies_user_id', '', time() + 60 * 60 * 24 * 30, '/', true, true);
    }

    public function updateStoredUser(int|UserEntity $user): bool
    {
        if (isset($_SESSION['cmwUser']) && $_SESSION['cmwUser'] instanceof UserEntity) {
            if ($user instanceof UserEntity) {
                $_SESSION['cmwUser'] = $user;
                return true;
            }

            $newUser = UsersModel::getInstance()->getUserById($user);
            $_SESSION['cmwUser'] = $newUser;

            return $newUser !== null;
        }

        return false;
    }
}

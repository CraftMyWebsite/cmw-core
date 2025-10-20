<?php

namespace CMW\Implementation\Users\Users;

use CMW\Entity\Users\UserEntity;
use CMW\Interface\Users\IUsersSession;
use CMW\Model\Users\UsersModel;
use CMW\Model\Users\UsersRememberTokensModel;

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
        // 1. Check session first (fastest method)
        if (isset($_SESSION['cmwUser']) && $_SESSION['cmwUser'] instanceof UserEntity) {
            $user = $_SESSION['cmwUser'];

            // Verify session is still valid by checking if user has at least one valid token
            // This allows admins to force-logout users by revoking all their tokens
            if (isset($_SESSION['cmw_session_validated_at'])) {
                $lastValidation = $_SESSION['cmw_session_validated_at'];
                $timeSinceValidation = time() - $lastValidation;

                // Only re-validate every 30 seconds to avoid excessive DB queries
                if ($timeSinceValidation < 30) {
                    return $user;
                }
            }

            // Check if user still has valid tokens (means session wasn't force-revoked by admin)
            $tokenModel = UsersRememberTokensModel::getInstance();
            $userTokens = $tokenModel->getUserTokens($user->getId());

            // If admin revoked all tokens, force logout the user
            if (empty($userTokens)) {
                // Clear the cookie too
                $this->clearRememberMeCookie();
                // Force logout
                $this->logOut();
                return null;
            }

            // Mark session as validated
            $_SESSION['cmw_session_validated_at'] = time();

            return $user;
        }

        // 2. Try secure remember-me token system
        if (isset($_COOKIE['cmw_remember_token'])) {
            $user = $this->validateRememberMeToken();
            if ($user !== null) {
                return $user;
            }
        }

        // 3. Fallback to legacy cookie system (for backwards compatibility)
        // TODO: Remove this after migration period
        if (isset($_COOKIE['cmw_cookies_user_id']) && filter_var($_COOKIE['cmw_cookies_user_id'], FILTER_VALIDATE_INT)) {
            $user = UsersModel::getInstance()->getUserById($_COOKIE['cmw_cookies_user_id']);

            // Migrate to new token system
            if ($user !== null) {
                $this->migrateLegacyCookie($user);
            }

            return $user;
        }

        return null;
    }

    /**
     * @return UserEntity|null
     * @description Validate remember-me token and restore session
     */
    private function validateRememberMeToken(): ?UserEntity
    {
        $cookieValue = $_COOKIE['cmw_remember_token'];

        // Token format: selector:token
        $parts = explode(':', $cookieValue, 2);
        if (count($parts) !== 2) {
            $this->clearRememberMeCookie();
            return null;
        }

        [$selector, $token] = $parts;

        $tokenModel = UsersRememberTokensModel::getInstance();
        $userId = $tokenModel->validateToken($selector, $token);

        if ($userId === null) {
            $this->clearRememberMeCookie();
            return null;
        }

        $user = UsersModel::getInstance()->getUserById($userId);

        if ($user === null) {
            return null;
        }

        // Token rotation: Generate new token for security
        $newTokenData = $tokenModel->generateToken();
        if ($tokenModel->storeToken($userId, $newTokenData['selector'], $newTokenData['token'])) {
            $this->setRememberMeCookie($newTokenData['selector'], $newTokenData['token']);
        }

        // Restore session
        $_SESSION['cmwUser'] = $user;

        return $user;
    }

    /**
     * @param UserEntity $user
     * @return void
     * @description Migrate legacy cookie to new secure token system
     */
    private function migrateLegacyCookie(UserEntity $user): void
    {
        // Delete old insecure cookie
        setcookie('cmw_cookies_user_id', '', time() - 3600, '/');

        // Create new secure token
        $tokenModel = UsersRememberTokensModel::getInstance();
        $tokenData = $tokenModel->generateToken();

        if ($tokenModel->storeToken($user->getId(), $tokenData['selector'], $tokenData['token'])) {
            $this->setRememberMeCookie($tokenData['selector'], $tokenData['token']);
        }

        // Restore session
        $_SESSION['cmwUser'] = $user;
    }

    /**
     * @param string $selector Token selector
     * @param string $token Token secret
     * @return void
     * @description Set secure remember-me cookie
     */
    private function setRememberMeCookie(string $selector, string $token): void
    {
        $cookieValue = $selector . ':' . $token;
        $expires = time() + (30 * 24 * 60 * 60); // 30 days

        setcookie(
            'cmw_remember_token',
            $cookieValue,
            [
                'expires' => $expires,
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );
    }

    /**
     * @return void
     * @description Clear remember-me cookie
     */
    private function clearRememberMeCookie(): void
    {
        if (isset($_COOKIE['cmw_remember_token'])) {
            setcookie('cmw_remember_token', '', time() - 3600, '/');
            unset($_COOKIE['cmw_remember_token']);
        }
    }

    public function logOut(): void
    {
        // Delete remember-me token from database if exists
        if (isset($_COOKIE['cmw_remember_token'])) {
            $parts = explode(':', $_COOKIE['cmw_remember_token'], 2);
            if (\count($parts) === 2) {
                UsersRememberTokensModel::getInstance()->deleteTokenBySelector($parts[0]);
            }
        }

        // Clear remember-me cookie
        $this->clearRememberMeCookie();

        // Clear legacy cookie if exists
        if (isset($_COOKIE['cmw_cookies_user_id'])) {
            setcookie('cmw_cookies_user_id', '', time() - 3600, '/');
        }

        // Clear session
        $_SESSION = [];
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']);
        session_destroy();
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

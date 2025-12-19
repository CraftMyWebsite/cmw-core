<?php

namespace CMW\Model\Users;

use CMW\Entity\Users\UserTokenEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use Random\RandomException;

/**
 * Class: @UsersRememberTokensModel
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @description Manages secure remember-me tokens for persistent user authentication
 */
class UsersRememberTokensModel extends AbstractModel
{
    /**
     * @return array{selector: string, token: string}
     * @description Generate cryptographically secure remember-me token
     * The token is split into two parts:
     * - selector: public identifier (stored in DB and cookie)
     * - token: secret part (hashed in DB, stored in plain in cookie)
     */
    public function generateToken(): array
    {
        try {

            $selector = bin2hex(random_bytes(16));
            $token = bin2hex(random_bytes(32));
        } catch (RandomException) {
            // Fallback
            $selector = bin2hex(openssl_random_pseudo_bytes(16));
            $token = bin2hex(openssl_random_pseudo_bytes(32));
        }

        return [
            'selector' => $selector,
            'token' => $token
        ];
    }

    /**
     * @param int $userId User ID to associate token with
     * @param string $selector Public token identifier
     * @param string $token Secret token (will be hashed before storage)
     * @param int $daysValid Number of days the token remains valid (default: 30)
     * @return bool Success status
     * @description Store token in database with security metadata
     */
    public function storeToken(int $userId, string $selector, string $token, int $daysValid = 30): bool
    {
        $tokenHash = password_hash($token, PASSWORD_BCRYPT);
        $expiresAt = date('Y-m-d H:i:s', strtotime("+$daysValid days"));

        $sql = 'INSERT INTO cmw_users_remember_tokens
                (user_id, token_selector, token_hash, user_agent, ip_address, expires_at)
                VALUES (:user_id, :selector, :hash, :user_agent, :ip, :expires)';

        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute([
            'user_id' => $userId,
            'selector' => $selector,
            'hash' => $tokenHash,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            'expires' => $expiresAt
        ]);
    }

    /**
     * @param string $selector Public token identifier
     * @param string $token Secret token to verify
     * @return int|null User ID if token is valid, null otherwise
     * @description Validate token and return associated user ID
     * Security features:
     * - Checks token hasn't expired
     * - Verifies token hash using constant-time comparison
     * - Automatically deletes token after use (token rotation)
     * - Deletes token if hash doesn't match (potential attack)
     */
    public function validateToken(string $selector, string $token): ?int
    {
        $sql = 'SELECT user_id, token_hash, expires_at
                FROM cmw_users_remember_tokens
                WHERE token_selector = :selector
                AND expires_at > NOW()';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(['selector' => $selector])){
            return null;
        }

        $result = $req->fetch();

        if (!$result) {
            return null;
        }

        $this->deleteTokenBySelector($selector);
        if (!password_verify($token, $result['token_hash'])) {
            return null;
        }

        return (int)$result['user_id'];
    }

    /**
     * @param string $selector Token selector to delete
     * @return bool Success status
     * @description Delete specific token by its selector
     */
    public function deleteTokenBySelector(string $selector): bool
    {
        $sql = 'DELETE FROM cmw_users_remember_tokens WHERE token_selector = :selector';
        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute(['selector' => $selector]);
    }

    /**
     * @param int $userId User ID
     * @return bool Success status
     * @description Delete all tokens for a user (logout from all devices)
     */
    public function deleteAllUserTokens(int $userId): bool
    {
        $sql = 'DELETE FROM cmw_users_remember_tokens WHERE user_id = :user_id';
        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute(['user_id' => $userId]);
    }

    /**
     * @return int Number of deleted tokens
     * @description Clean expired tokens from database
     * Should be called periodically via cron job
     */
    public function cleanExpiredTokens(): int
    {
        $sql = 'DELETE FROM cmw_users_remember_tokens WHERE expires_at < NOW()';
        $db = DatabaseManager::getInstance();
        return $db->query($sql)->rowCount();
    }

    /**
     * @param int $userId User ID
     * @return UserTokenEntity[] List of active tokens with metadata
     * @description Get all active tokens for a user (for UI display in profile/security settings)
     */
    public function getUserTokens(int $userId): array
    {
        $sql = 'SELECT token_id, user_id, token_selector, user_agent, ip_address, created_at, last_used_at, expires_at
                FROM cmw_users_remember_tokens
                WHERE user_id = :user_id AND expires_at > NOW()
                ORDER BY last_used_at DESC';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute(['user_id' => $userId]);
        $results = $req->fetchAll();

        $tokens = [];
        foreach ($results as $result) {
            $tokens[] = new UserTokenEntity(
                $result['token_id'],
                $result['user_id'],
                $result['token_selector'],
                $result['user_agent'] ?? '',
                $result['ip_address'] ?? '',
                $result['created_at'],
                $result['last_used_at'],
                $result['expires_at']
            );
        }

        return $tokens;
    }

    /**
     * @param int $tokenId Token ID to revoke
     * @param int $userId User ID (security check - can only revoke own tokens)
     * @return bool Success status
     * @description Revoke specific token by ID (allows user to logout specific device)
     */
    public function revokeToken(int $tokenId, int $userId): bool
    {
        $sql = 'DELETE FROM cmw_users_remember_tokens
                WHERE token_id = :token_id AND user_id = :user_id';
        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute([
            'token_id' => $tokenId,
            'user_id' => $userId
        ]);
    }

    /**
     * @param int $userId User ID
     * @return int Number of active tokens
     * @description Count active tokens for a user
     */
    public function countUserTokens(int $userId): int
    {
        $sql = 'SELECT COUNT(*) as count
                FROM cmw_users_remember_tokens
                WHERE user_id = :user_id AND expires_at > NOW()';

        $db = DatabaseManager::getInstance();
        $stmt = $db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch();

        return (int)($result['count'] ?? 0);
    }
}

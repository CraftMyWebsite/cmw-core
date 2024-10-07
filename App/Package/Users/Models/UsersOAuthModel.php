<?php

namespace CMW\Model\Users;

use CMW\Entity\Users\UserEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;

class UsersOAuthModel extends AbstractModel
{
    /**
     * @param string $methode
     * @return bool
     */
    public function enableOAuthImplementation(string $methode): bool
    {
        $sql = "INSERT INTO `cmw_users_oauth_methods_enabled` (`methode`) VALUES (:methode)";
        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute(['methode' => $methode]);
    }

    /**
     * @return bool
     * @desc Clear all enabled OAuth implementations
     */
    public function clearOAuthImplementationsEnabled(): bool
    {
        $sql = "DELETE FROM `cmw_users_oauth_methods_enabled`";
        return DatabaseManager::getInstance()->prepare($sql)->execute();
    }

    public function isMethodEnabled(string $methode): bool
    {
        $sql = "SELECT * FROM `cmw_users_oauth_methods_enabled` WHERE `methode` = :methode";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(['methode' => $methode])) {
            return false;
        }

        return $req->rowCount() > 0;
    }

    /**
     * @return array
     */
    public function getMethodEnabled(): array
    {
        $sql = "SELECT methode FROM `cmw_users_oauth_methods_enabled`";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute()) {
            return [];
        }

        $res = $req->fetchAll(\PDO::FETCH_COLUMN, 0);

        if (!$res) {
            return [];
        }

        return $res;
    }

    /**
     * @param string $oAuthId
     * @param string $methode
     * @param string $mail
     * @return \CMW\Entity\Users\UserEntity|null
     */
    public function getUser(string $oAuthId, string $mail, string $methode): ?UserEntity
    {
        $data = [
            'mail' => $mail,
            'data' => $oAuthId,
            'methode' => $methode,
        ];

        $sql = 'SELECT cmw_users.user_id FROM cmw_users_oauth 
                JOIN cmw_users ON cmw_users.user_id = cmw_users_oauth.user_id
                WHERE user_email = :mail
                    AND data = :data 
                    AND methode = :methode';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);


        if (!$req->execute($data)) {
            return null;
        }

        $res = $req->fetch();

        if (!$res) {
            return null;
        }

        return UsersModel::getInstance()->getUserById($res['user_id']);
    }

    /**
     * @param int $userId
     * @param string $oAuthId
     * @param string $methode
     * @return bool
     */
    public function createUser(int $userId, string $oAuthId, string $methode): bool
    {
        $data = [
            'user_id' => $userId,
            'oauth_id' => $oAuthId,
            'methode' => $methode,
        ];

        $sql = 'INSERT INTO cmw_users_oauth (user_id, data, methode) VALUES (:user_id, :oauth_id, :methode)';

        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute($data);
    }
}
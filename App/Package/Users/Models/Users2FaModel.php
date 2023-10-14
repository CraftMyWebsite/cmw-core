<?php

namespace CMW\Model\Users;

use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;

/**
 * Class: @Users2FaModel
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class Users2FaModel extends AbstractModel
{

    /**
     * @param int $userId
     * @param string $secret
     * @return bool
     * @desc This methode is only used when we are creating a new user.
     */
    public function create(int $userId, string $secret): bool
    {
        $sql = "INSERT INTO cmw_users_2fa (users_2fa_user_id, users_2fa_is_enabled, users_2fa_secret) 
                VALUES (:userId, 0, :secret)";
        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute(['userId' => $userId, 'secret' => $secret]);
    }


    /**
     * @param int $userId
     * @param int $status
     * @return bool
     */
    public function toggle2Fa(int $userId, int $status): bool
    {
        $sql = "UPDATE cmw_users_2fa SET users_2fa_is_enabled = :status WHERE users_2fa_user_id = :userId";
        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute(['userId' => $userId, 'status' => $status]);
    }

    /**
     * @param int $userId
     * @param string $secret
     * @return bool
     */
    public function updateSecret(int $userId, string $secret): bool
    {
        $sql = "UPDATE cmw_users_2fa SET users_2fa_secret = :secret WHERE users_2fa_user_id = :userId";
        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute(['userId' => $userId, 'secret' => $secret]);
    }
}
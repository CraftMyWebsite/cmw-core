<?php

namespace CMW\Model\Users;

use CMW\Entity\Users\UserPictureEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Manager\Uploads\ImagesManager;

/**
 * Class: @UserPictureModel
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class UserPictureModel extends AbstractModel
{
    /**
     * @param int $userId
     * @param string $imageName
     * @return UserPictureEntity|null
     */
    public function uploadImage(int $userId, string $imageName): ?UserPictureEntity
    {
        // First check if the user has an image
        if ($this->userHasImage($userId)) {
            return $this->updateUserImage($userId, $imageName);
        }

        $sql = 'INSERT INTO cmw_users_pictures (users_pictures_user_id, users_pictures_image_name) VALUES (:userId, :imageName)';
        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(['userId' => $userId, 'imageName' => $imageName])) {
            return null;
        }

        return $this->getImageByUserId($userId);
    }

    /**
     * @param int $userId
     * @return bool
     * @desc Check if the player already has an image
     */
    public function userHasImage(int $userId): bool
    {
        $sql = 'SELECT users_pictures_user_id FROM `cmw_users_pictures` WHERE users_pictures_user_id = :userId';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        return $req->execute(['userId' => $userId]) && count($req->fetchAll()) >= 1;
    }

    public function userHasDefaultImage(int $userId): bool
    {
        return is_file(EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/Users/Default/' . UsersSettingsModel::getInstance()->getSetting('defaultImage')) && !$this->userHasImage($userId);
    }

    /**
     * @param int $userId
     * @param string $imageName
     * @return UserPictureEntity|null
     */
    public function updateUserImage(int $userId, string $imageName): ?UserPictureEntity
    {

        // Delete older image if this isn't the Default image
        if (!$this->userHasDefaultImage($userId)) {
            // Get older imageName
            $userPictureEntity = $this->getImageByUserId($userId);
            $olderImageName = $userPictureEntity?->getImage();

            ImagesManager::deleteImage($olderImageName, 'Users');
        }

        $sql = 'UPDATE cmw_users_pictures SET users_pictures_image_name = :imageName, 
                                            users_pictures_last_update = CURRENT_TIMESTAMP() 
                                            WHERE users_pictures_user_id = :userId';
        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(['userId' => $userId, 'imageName' => $imageName])) {
            return null;
        }

        return $this->getImageByUserId($userId);
    }

    public function getImageByUserId(int $userId): ?UserPictureEntity
    {
        $sql = 'SELECT * FROM cmw_users_pictures WHERE users_pictures_user_id = :userId';
        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(['userId' => $userId])) {
            return null;
        }

        $res = $req->fetch();

        return new UserPictureEntity(
            $res['users_pictures_user_id'],
            $res['users_pictures_image_name'],
            $res['users_pictures_last_update']
        );
    }

    public function deleteUserPicture(int $userId): bool
    {
        if ($this->userHasDefaultImage($userId)) {
            return false;
        }

        $imageName = $this->getImageByUserId($userId)?->getImage();

        if (is_null($imageName)) {
            return false;
        }

        ImagesManager::deleteImage($imageName, 'Users');

        $sql = 'DELETE FROM cmw_users_pictures WHERE users_pictures_user_id = :userId';
        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute(['userId' => $userId]);
    }
}

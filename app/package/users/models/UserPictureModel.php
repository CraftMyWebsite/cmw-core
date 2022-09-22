<?php

namespace CMW\Model\Users;

use CMW\Entity\Users\UserPictureEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Utils\Images;


/**
 * Class: @UserPictureModel
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class UserPictureModel extends DatabaseManager
{


    /**
     * @param int $userId
     * @param array $image
     * @return \CMW\Entity\Users\UserPictureEntity|null
     * @throws \Exception
     */
    public function uploadImage(int $userId, array $image): ?UserPictureEntity
    {
        //First check if the user has an image
        if ($this->userHasImage($userId)) {
            return $this->updateUserImage($userId, $image);
        }


        //Upload image on the server
        $imageName = Images::upload($image, 'users');

        $sql = "INSERT INTO cmw_users_pictures (users_pictures_user_id, users_pictures_image_name) VALUES (:userId, :imageName)";
        $db = self::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(array("userId" => $userId, "imageName" => $imageName))) {
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
        $sql = "SELECT users_pictures_user_id FROM `cmw_users_pictures` WHERE users_pictures_user_id = :userId";

        $db = self::getInstance();
        $req = $db->prepare($sql);

        return $req->execute(array('userId' => $userId)) && count($req->fetchAll()) >= 1;
    }

    /**
     * @param int $userId
     * @param array $image
     * @return \CMW\Entity\Users\UserPictureEntity|null
     * @throws \Exception
     */
    public function updateUserImage(int $userId, array $image): ?UserPictureEntity
    {

        //Get older imageName
        $userPictureEntity = $this->getImageByUserId($userId);


        $olderImageName = $userPictureEntity?->getImageName();

        //Delete older image
        Images::deleteImage($olderImageName, 'users');

        //Upload image on the server
        $imageName = Images::upload($image, 'users');


        $sql = "UPDATE cmw_users_pictures SET users_pictures_image_name = :imageName, 
                                            users_pictures_last_update = CURRENT_TIMESTAMP() 
                                            WHERE users_pictures_user_id = :userId";
        $db = self::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(array("userId" => $userId, "imageName" => $imageName))) {
            return null;
        }

        return $this->getImageByUserId($userId);
    }

    public function getImageByUserId(int $userId): ?UserPictureEntity
    {
        $sql = "SELECT * FROM cmw_users_pictures WHERE users_pictures_user_id = :userId";
        $db = self::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(array("userId" => $userId))) {
            return null;
        }

        $res = $req->fetch();

        return new UserPictureEntity(
            $res['users_pictures_user_id'],
            $res['users_pictures_image_name'],
            $res['users_pictures_last_update']
        );
    }

}
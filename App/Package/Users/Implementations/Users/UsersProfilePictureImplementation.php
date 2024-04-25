<?php

namespace CMW\Implementation\Users\Users;

use CMW\Controller\Users\UsersSettingsController;
use CMW\Entity\Users\UserPictureEntity;
use CMW\Interface\Users\IUsersProfilePicture;
use CMW\Manager\Env\EnvManager;
use CMW\Model\Users\UserPictureModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;

class UsersProfilePictureImplementation implements IUsersProfilePicture
{

    public function weight(): int
    {
        return 1;
    }

    #[NoReturn] public function changeMethod(mixed $picture, int $userId): void
    {
        UserPictureModel::getInstance()->uploadImage($userId, $picture);

        UsersModel::updateStoredUser(UsersModel::getInstance()->getUserById($userId));

        Redirect::redirectPreviousRoute();
    }

    public function deleteUserProfilePicture(int $userId): bool
    {
        return UserPictureModel::getInstance()->deleteUserPicture($userId);
    }

    #[NoReturn] public function resetPicture(int $userId): void
    {
        UserPictureModel::getInstance()->deleteUserPicture($userId);

        Redirect::redirectPreviousRoute();
    }

    public function getUserProfilePicture(int $userId): UserPictureEntity
    {
        if (UserPictureModel::getInstance()->userHasImage($userId)) {
            $img = UserPictureModel::getInstance()->getImageByUserId($userId);
            $imgPath = EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . "Public/Uploads/Users/" . $img?->getImage();

            return new UserPictureEntity(
                $userId,
                $imgPath,
                $img?->getLastUpdate()
            );
        }

        return new UserPictureEntity(
            $userId,
            $this->getDefaultProfilePicture(),
            null,
        );

    }

    public function getDefaultProfilePicture(): string
    {
        return UsersSettingsController::getDefaultImageLink();
    }
}
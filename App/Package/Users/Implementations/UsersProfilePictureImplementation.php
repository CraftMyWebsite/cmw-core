<?php

namespace CMW\Implementation\Users;

use CMW\Interface\Users\IUsersProfilePicture;
use CMW\Model\Users\UserPictureModel;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;

class UsersProfilePictureImplementation implements  IUsersProfilePicture {

    public function weight(): int
    {
        return 1;
    }

    #[NoReturn] public function changeMethod(mixed $picture, int $userId): void
    {
        UserPictureModel::getInstance()->uploadImage($userId, $picture);

        Redirect::redirectPreviousRoute();
    }

    public function deleteUserProfilePicture(): bool
    {
        // TODO: Implement deleteUserProfilePicture() method.
        return true;
    }

    public function getUserProfilePicture(int $userId): string
    {
        // TODO: Implement getUserProfilePicture() method.
        return "";
    }

    public function resetPicture(int $userId): void
    {
        UserPictureModel::getInstance()->deleteUserPicture($userId);

        Redirect::redirectPreviousRoute();
    }
}
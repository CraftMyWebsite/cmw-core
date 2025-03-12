<?php

namespace CMW\Implementation\Users\Users;

use CMW\Controller\Users\UsersSessionsController;
use CMW\Entity\Users\UserPictureEntity;
use CMW\Interface\Users\IUsersProfilePicture;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Uploads\ImagesException;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Model\Users\UserPictureModel;
use CMW\Model\Users\UsersModel;
use CMW\Model\Users\UsersSettingsModel;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;

class UsersProfilePictureImplementation implements IUsersProfilePicture
{
    public function weight(): int
    {
        return 1;
    }

    #[NoReturn]
    public function changeMethod(mixed $picture, int $userId): void
    {
        $user = UsersModel::getInstance()->getUserById($userId);

        if ($user === null) {
            Flash::send(Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                LangManager::translate('core.errors.user.not.found'),
            );
            Redirect::redirectPreviousRoute();
        }

        try {
            // Upload image on the server
            $imageName = ImagesManager::convertAndUpload($picture, 'Users');

            if (!UserPictureModel::getInstance()->uploadImage($user->getId(), $imageName)) {
                Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                    LangManager::translate('core.errors.upload.image'));
                Redirect::redirectPreviousRoute();
            }
        } catch (ImagesException $e) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.errors.upload.image') . " => $e");
        }

        UsersSessionsController::getInstance()->updateStoredUser($userId);

        Redirect::redirectPreviousRoute();
    }

    public function deleteUserProfilePicture(int $userId): bool
    {
        return UserPictureModel::getInstance()->deleteUserPicture($userId);
    }

    #[NoReturn]
    public function resetPicture(int $userId): void
    {
        UserPictureModel::getInstance()->deleteUserPicture($userId);

        Redirect::redirectPreviousRoute();
    }

    public function getUserProfilePicture(int $userId): UserPictureEntity
    {
        if (UserPictureModel::getInstance()->userHasImage($userId)) {
            $img = UserPictureModel::getInstance()->getImageByUserId($userId);
            $imgPath = EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Public/Uploads/Users/' . $img?->getImage();

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
        $defaultImg = UsersSettingsModel::getInstance()->getSetting('defaultImage');
        return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Public/Uploads/Users/Default/' . $defaultImg;
    }
}

<?php

namespace CMW\Interface\Users;


use CMW\Entity\Users\UserPictureEntity;

interface IUsersProfilePicture
{

    /**
     * @return int
     * @desc Return the weight of this implementation.
     *       The CMS select the implementation with the highest weight.
     */
    public function weight(): int;

    /**
     * @param mixed $picture
     * @param int $userId
     * @return void
     * @desc This is the method to change the user profile picture
     */
    public function changeMethod(mixed $picture, int $userId): void;

    /**
     * @param int $userId
     * @return void
     * @desc This method reset the user profile picture
     */
    public function resetPicture(int $userId): void;

    /**
     * @param int $userId
     * @return bool
     * @desc This methode is when you want to delete a profile picture
     */
    public function deleteUserProfilePicture(int $userId): bool;

    /**
     * @param int $userId
     * @return UserPictureEntity
     * @desc Return the profile picture entity @UserPictureEntity
     */
    public function getUserProfilePicture(int $userId): UserPictureEntity;

    /**
     * @return string
     * @desc Return the default profile picture link
     */
    public function getDefaultProfilePicture(): string;
}
<?php

namespace CMW\Interface\Users;


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
     * @return bool
     * @desc This methode is when you want to delete a profile picture
     */
    public function deleteUserProfilePicture(): bool;

    /**
     * @param int $userId
     * @return string
     * @desc Return the profile picture link
     */
    public function getUserProfilePicture(int $userId): string;
}
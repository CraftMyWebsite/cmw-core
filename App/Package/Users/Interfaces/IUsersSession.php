<?php

namespace CMW\Interface\Users;

use CMW\Entity\Users\UserEntity;

/**
 * Interface: @IUsersSession
 * @package Users
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/interfaces
 */
interface IUsersSession
{
    /**
     * @return int
     * @desc Get the weight of the current implementation
     */
    public function weight(): int;

    /**
     * @return UserEntity|null
     * @desc Get the current user
     */
    public function getCurrentUser(): ?UserEntity;

    /**
     * @param int|UserEntity $user
     * @return bool
     * @desc This method is useful to update session user. If you pass @UserEntity, we are using this User instance.
     */
    public function updateStoredUser(int|UserEntity $user): bool;

    /**
     * @return void
     * @desc Log out the current user
     */
    public function logOut(): void;
}

<?php

namespace CMW\Controller\Users;

use CMW\Entity\Users\UserEntity;
use CMW\Interface\Users\IUsersSession;
use CMW\Manager\Loader\Loader;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;

/**
 * Class: @UsersSessionsController
 * @package Users
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/controllers
 */
class UsersSessionsController extends AbstractController
{
    private IUsersSession $implementation;

    public function __construct()
    {
        $this->implementation = Loader::getHighestImplementation(IUsersSession::class);
    }

    /**
     * @return void
     */
    public function logOut(): void
    {
        $this->implementation->logOut();
    }

    /**
     * @param int|UserEntity $user
     * @return bool
     */
    public function updateStoredUser(int|UserEntity $user): bool
    {
        return $this->implementation->updateStoredUser($user);
    }

    /**
     * @return UserEntity|null
     */
    public function getCurrentUser(): ?UserEntity
    {
        return $this->implementation->getCurrentUser();
    }
}

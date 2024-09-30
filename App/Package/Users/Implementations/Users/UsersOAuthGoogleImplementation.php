<?php

namespace CMW\Implementation\Users\Users;

use CMW\Interface\Users\IUsersOAuth;
use CMW\Manager\Env\EnvManager;

class UsersOAuthGoogleImplementation implements IUsersOAuth
{

    public function methodeName(): string
    {
        return "Google";
    }

    public function methodIdentifier(): string
    {
        return "google";
    }

    public function methodeIconLink(): string
    {
        return EnvManager::getInstance()->getValue('PATH_URL')
            . 'App/Package/Users/Implementations/Users/Assets/OAuth/google.png';
    }

    public function register(): bool
    {
        // TODO: Implement register() method.



        return false;
    }

    public function login(): bool
    {
        // TODO: Implement login() method.
        return false;
    }
}
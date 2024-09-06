<?php

namespace CMW\Implementation\Users\Core;

use CMW\Interface\Core\IMenus;
use CMW\Manager\Lang\LangManager;

class UsersMenusImplementations implements IMenus
{
    public function getRoutes(): array
    {
        return [
            LangManager::translate('users.login.title') => 'login',
            LangManager::translate('users.register.title') => 'register',
            LangManager::translate('users.login.forgot_password.title') => 'login/forgot',
            LangManager::translate('users.profile') => 'profile'
        ];
    }

    public function getPackageName(): string
    {
        return 'Users';
    }
}

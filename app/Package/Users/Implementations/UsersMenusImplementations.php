<?php

namespace CMW\Implementation\Users;

use CMW\Interface\Core\IMenus;

class UsersMenusImplementations implements IMenus {

    public function getRoutes(): array
    {
        return [
            'login',
            'register',
            'login/forgot',
            'profile'
        ];
    }

    public function getPackageName(): string
    {
        return 'Users';
    }
}
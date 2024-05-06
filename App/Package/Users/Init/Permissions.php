<?php

namespace CMW\Permissions\Users;

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Permission\IPermissionInit;
use CMW\Manager\Permission\PermissionInitType;

class Permissions implements IPermissionInit
{
    public function permissions(): array
    {
        return [
            new PermissionInitType(
                code: 'users.edit',
                description: LangManager::translate('users.permissions.users.edit'),
            ),
            new PermissionInitType(
                code: 'users.add',
                description: LangManager::translate('users.permissions.users.add'),
            ),
            new PermissionInitType(
                code: 'users.delete',
                description: LangManager::translate('users.permissions.users.delete'),
            ),
            new PermissionInitType(
                code: 'users.settings',
                description: LangManager::translate('users.permissions.users.settings.manage'),
            ),
            new PermissionInitType(
                code: 'users.settings.blacklist.pseudo',
                description: LangManager::translate('users.permissions.users.settings.blacklist.pseudo'),
            ),
            new PermissionInitType(
                code: 'users.roles.manage',
                description: LangManager::translate('users.permissions.users.roles.manage'),
            ),
            new PermissionInitType(
                code: 'users.roles.add',
                description: LangManager::translate('users.permissions.users.roles.add'),
            ),
            new PermissionInitType(
                code: 'users.roles.edit',
                description: LangManager::translate('users.permissions.users.roles.edit'),
            ),
            new PermissionInitType(
                code: 'users.roles.delete',
                description: LangManager::translate('users.permissions.users.roles.delete'),
            ),
        ];
    }

}
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
                code: 'users.settings',
                description: LangManager::translate('users.permissions.settings.manage'),
            ),
            new PermissionInitType(
                code: 'users.settings.blacklist.add',
                description: LangManager::translate('users.permissions.settings.blacklist.add'),
            ),
            new PermissionInitType(
                code: 'users.settings.blacklist.edit',
                description: LangManager::translate('users.permissions.settings.blacklist.edit'),
            ),
            new PermissionInitType(
                code: 'users.settings.blacklist.delete',
                description: LangManager::translate('users.permissions.settings.blacklist.delete'),
            ),
            new PermissionInitType(
                code: 'users.manage',
                description: LangManager::translate('users.permissions.users.manage'),
            ),
            new PermissionInitType(
                code: 'users.manage.add',
                description: LangManager::translate('users.permissions.users.add'),
            ),
            new PermissionInitType(
                code: 'users.manage.edit',
                description: LangManager::translate('users.permissions.users.edit'),
            ),
            new PermissionInitType(
                code: 'users.manage.delete',
                description: LangManager::translate('users.permissions.users.delete'),
            ),
            new PermissionInitType(
                code: 'users.roles',
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

<?php

namespace CMW\Permissions\Pages;

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Permission\IPermissionInit;
use CMW\Manager\Permission\PermissionInitType;

class Permissions implements IPermissionInit
{
    public function permissions(): array
    {
        return [
            new PermissionInitType(
                code: 'pages.show',
                description: LangManager::translate('pages.permissions.pages.show'),
            ),
            new PermissionInitType(
                code: 'pages.edit',
                description: LangManager::translate('pages.permissions.pages.edit'),
            ),
            new PermissionInitType(
                code: 'pages.add',
                description: LangManager::translate('pages.permissions.pages.add'),
            ),
            new PermissionInitType(
                code: 'pages.delete',
                description: LangManager::translate('pages.permissions.pages.delete'),
            ),
        ];
    }

}
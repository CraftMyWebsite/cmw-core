<?php

namespace CMW\Permissions\Core;

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Permission\IPermissionInit;
use CMW\Manager\Permission\PermissionInitType;

class Permissions implements IPermissionInit
{
    public function permissions(): array
    {
        return [
            new PermissionInitType(
                code: 'core.dashboard',
                description: LangManager::translate('core.permissions.core.dashboard'),
            ),
            new PermissionInitType(
                code: 'core.configuration',
                description: LangManager::translate('core.permissions.core.configuration.show'),
            ),
            new PermissionInitType(
                code: 'core.condition.edit',
                description: LangManager::translate('core.permissions.core.condition.edit'),
            ),
            new PermissionInitType(
                code: 'core.editor.edit',
                description: LangManager::translate('core.permissions.core.editor.edit'),
            ),
            new PermissionInitType(
                code: 'core.mail.configuration',
                description: LangManager::translate('core.permissions.core.mail.configuration'),
            ),
            new PermissionInitType(
                code: 'core.menus.configuration',
                description: LangManager::translate('core.permissions.core.menus.configuration'),
            ),
            new PermissionInitType(
                code: 'core.security.configuration',
                description: LangManager::translate('core.permissions.core.security.configuration'),
            ),
            new PermissionInitType(
                code: 'core.theme.configuration',
                description: LangManager::translate('core.permissions.core.theme.configuration'),
            ),
            new PermissionInitType(
                code: 'core.update',
                description: LangManager::translate('core.permissions.core.update'),
            ),
            new PermissionInitType(
                code: 'core.maintenance',
                description: LangManager::translate('core.permissions.core.maintenance.edit'),
            ),
            new PermissionInitType(
                code: 'core.maintenance.bypass',
                description: LangManager::translate('core.permissions.core.maintenance.bypass'),
            ),
            new PermissionInitType(
                code: 'core.packages.local',
                description: LangManager::translate('core.permissions.core.packages.local'),
            ),
            new PermissionInitType(
                code: 'core.packages.market',
                description: LangManager::translate('core.permissions.core.packages.market'),
            ),
        ];
    }

}
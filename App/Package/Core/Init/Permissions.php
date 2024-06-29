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
            /*DASHBOARD*/
            new PermissionInitType(
                code: 'core.dashboard',
                description: LangManager::translate('core.permissions.dashboard'),
            ),
            /*SETTINGS*/
            new PermissionInitType(
                code: 'core.settings',
                description: LangManager::translate('core.permissions.settings.title'),
            ),
            new PermissionInitType(
                code: 'core.settings.website',
                description: LangManager::translate('core.permissions.settings.website'),
            ),
            new PermissionInitType(
                code: 'core.settings.maintenance',
                description: LangManager::translate('core.permissions.settings.maintenance'),
            ),
            new PermissionInitType(
                code: 'core.settings.maintenance.bypass',
                description: LangManager::translate('core.permissions.settings.maintenance_bypass'),
            ),
            new PermissionInitType(
                code: 'core.settings.mails',
                description: LangManager::translate('core.permissions.settings.mails'),
            ),
            new PermissionInitType(
                code: 'core.settings.conditions',
                description: LangManager::translate('core.permissions.settings.conditions'),
            ),
            new PermissionInitType(
                code: 'core.settings.security',
                description: LangManager::translate('core.permissions.settings.security'),
            ),
            new PermissionInitType(
                code: 'core.settings.security.healthReport',
                description: LangManager::translate('core.permissions.settings.security_healthReport'),
            ),
            /*MENU*/
            new PermissionInitType(
                code: 'core.menu',
                description: LangManager::translate('core.permissions.menu'),
            ),
            /*MAJ*/
            new PermissionInitType(
                code: 'core.update',
                description: LangManager::translate('core.permissions.update'),
            ),
            /*THEMES*/
            new PermissionInitType(
                code: 'core.themes',
                description: LangManager::translate('core.permissions.themes.title'),
            ),
            new PermissionInitType(
                code: 'core.themes.edit',
                description: LangManager::translate('core.permissions.themes.edit'),
            ),
            new PermissionInitType(
                code: 'core.themes.manage',
                description: LangManager::translate('core.permissions.themes.manage'),
            ),
            new PermissionInitType(
                code: 'core.themes.market',
                description: LangManager::translate('core.permissions.themes.market'),
            ),
            /*PACKAGES*/
            new PermissionInitType(
                code: 'core.packages',
                description: LangManager::translate('core.permissions.packages.title'),
            ),
            new PermissionInitType(
                code: 'core.packages.manage',
                description: LangManager::translate('core.permissions.packages.manage'),
            ),
            new PermissionInitType(
                code: 'core.packages.market',
                description: LangManager::translate('core.permissions.packages.market'),
            ),
        ];
    }

}
<?php

namespace CMW\Package\Users;

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\IPackageConfig;
use CMW\Manager\Package\PackageMenuType;
use CMW\Manager\Package\PackageSubMenuType;

class Package implements IPackageConfig
{
    public function name(): string
    {
        return 'Users';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    public function authors(): array
    {
        return ['CraftMyWebsiteTeam'];
    }

    public function isGame(): bool
    {
        return false;
    }

    public function isCore(): bool
    {
        return true;
    }

    public function menus(): ?array
    {
        return [
            new PackageMenuType(
                icon: 'fas fa-user',
                title: LangManager::translate('core.menu.user.main'),
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: LangManager::translate('core.menu.user.settings'),
                        permission: 'users.settings',
                        url: null,
                        subMenus: [
                            new PackageSubMenuType(
                                title: LangManager::translate('users.pages.settings.general.menu'),
                                permission: 'users.settings',
                                url: 'users/settings/general',
                                subMenus: []
                            ),
                            new PackageSubMenuType(
                                title: LangManager::translate('users.pages.settings.security.menu'),
                                permission: 'users.settings',
                                url: 'users/settings/security',
                                subMenus: []
                            ),
                            new PackageSubMenuType(
                                title: LangManager::translate('users.pages.settings.blacklist.menu'),
                                permission: 'users.settings',
                                url: 'users/settings/blacklist/pseudo',
                                subMenus: []
                            ),
                        ]
                    ),
                    new PackageSubMenuType(
                        title: LangManager::translate('core.menu.user.manage'),
                        permission: 'users.manage',
                        url: 'users/manage',
                        subMenus: []
                    ),
                    new PackageSubMenuType(
                        title: LangManager::translate('core.menu.user.roles'),
                        permission: 'users.roles',
                        url: 'roles/manage',
                        subMenus: []
                    ),
                    new PackageSubMenuType(
                        title: 'oAuth',
                        permission: 'users.oauth',
                        url: 'users/oauth',
                        subMenus: []
                    ),
                ],
            ),
        ];
    }

    public function requiredPackages(): array
    {
        return ['Core'];
    }

    /**
     * @return bool
     * @desc <p>USers Package can't be delete.</p>
     */
    public function uninstall(): bool
    {
        // Return true, we don't need other operations for uninstall.
        return false;
    }
}

<?php

namespace CMW\Package\Core;

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\IPackageConfig;
use CMW\Manager\Package\PackageMenuType;
use CMW\Manager\Package\PackageSubMenuType;
use CMW\Manager\Theme\ThemeManager;

/**
 * @var $isUpToDate bool
 */

class Package implements IPackageConfig
{
    public function name(): string
    {
        return 'Core';
    }

    public function version(): string
    {
        return '0.0.1';
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
                icon: 'fas fa-gear',
                title: LangManager::translate('core.menu.setting.main'),
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: LangManager::translate('core.menu.setting.settings'),
                        permission: 'core.settings.website',
                        url: 'configuration',
                    ),
                    new PackageSubMenuType(
                        title: LangManager::translate('core.menu.setting.maintenance'),
                        permission: 'core.settings.maintenance',
                        url: 'maintenance/manage',
                    ),
                    new PackageSubMenuType(
                        title: LangManager::translate('core.menu.setting.mail'),
                        permission: 'core.settings.mails',
                        url: 'mail/configuration',
                    ),
                    new PackageSubMenuType(
                        title: LangManager::translate('core.menu.setting.cg'),
                        permission: 'core.settings.conditions',
                        url: 'condition',
                    ),
                    new PackageSubMenuType(
                        title: LangManager::translate('core.menu.setting.security'),
                        permission: 'core.settings.security',
                        url: 'security',
                    ),
                ],
            ),
            new PackageMenuType(
                icon: 'fas fa-bars',
                title: 'Menus',
                url: 'menus',
                permission: 'core.menu',
                subMenus: [],
            ),
            new PackageMenuType(
                icon: 'fas fa-cloud-arrow-down',
                title: $isUpToDate ? LangManager::translate('core.menu.update') : LangManager::translate('core.menu.updateMe'),
                url: 'updates/cms',
                permission: 'core.update',
                subMenus: [],
            ),
            new PackageMenuType(
                icon: 'fas fa-palette',
                title: LangManager::translate('core.menu.themes.main'),
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: LangManager::translate('core.menu.themes.edit') . ThemeManager::getInstance()->getCurrentTheme()->name(),
                        permission: 'core.themes.edit',
                        url: 'theme/manage',
                    ),
                    new PackageSubMenuType(
                        title: LangManager::translate('core.menu.themes.installed'),
                        permission: 'core.themes.manage',
                        url: 'theme/theme',
                    ),
                    new PackageSubMenuType(
                        title: LangManager::translate('core.menu.themes.market'),
                        permission: 'core.themes.market',
                        url: 'theme/market',
                    ),
                ],
            ),
            new PackageMenuType(
                icon: 'fas fa-puzzle-piece',
                title: LangManager::translate('core.menu.package.main'),
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: LangManager::translate('core.menu.package.installed'),
                        permission: 'core.packages.manage',
                        url: 'packages/package',
                    ),
                    new PackageSubMenuType(
                        title: LangManager::translate('core.menu.package.market'),
                        permission: 'core.packages.market',
                        url: 'packages/market',
                    ),
                ],
            ),
        ];
    }

    public function requiredPackages(): array
    {
        return [];
    }

    /**
     * @return bool
     * @desc <p>Core Package can't be delete.</p>
     */
    public function uninstall(): bool
    {
        // Return true, we don't need other operations for uninstall.
        return false;
    }
}

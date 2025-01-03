<?php

namespace CMW\Package\Core;

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\IPackageConfig;
use CMW\Manager\Package\PackageMenuType;
use CMW\Manager\Package\PackageSubMenuType;
use CMW\Manager\Theme\ThemeManager;
use CMW\Manager\Updater\UpdatesManager;

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
        $isUpToDate = UpdatesManager::checkNewUpdateAvailable();
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
                        subMenus: []
                    ),
                    new PackageSubMenuType(
                        title: LangManager::translate('core.menu.setting.maintenance'),
                        permission: 'core.settings.maintenance',
                        url: 'maintenance/manage',
                        subMenus: []
                    ),
                    new PackageSubMenuType(
                        title: LangManager::translate('core.menu.setting.mail'),
                        permission: 'core.settings.mails',
                        url: 'mail/configuration',
                        subMenus: []
                    ),
                    new PackageSubMenuType(
                        title: LangManager::translate('core.menu.setting.cg'),
                        permission: 'core.settings.conditions',
                        url: 'condition',
                        subMenus: []
                    ),
                    new PackageSubMenuType(
                        title: LangManager::translate('core.menu.setting.security'),
                        permission: 'core.settings.security',
                        url: 'security',
                        subMenus: []
                    ),
                ],
            ),
            new PackageMenuType(
                icon: 'fas fa-bars',
                title: 'Menus',
                url: 'menus',
                permission: 'core.menu'
            ),
            new PackageMenuType(
                icon: 'fas fa-cloud-arrow-down',
                title: $isUpToDate ? LangManager::translate('core.menu.updateMe') : LangManager::translate('core.menu.update'),
                url: 'updates/cms',
                permission: 'core.update'
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
                        subMenus: []
                    ),
                    new PackageSubMenuType(
                        title: LangManager::translate('core.menu.themes.installed'),
                        permission: 'core.themes.manage',
                        url: 'theme/theme',
                        subMenus: []
                    ),
                    new PackageSubMenuType(
                        title: LangManager::translate('core.menu.themes.market'),
                        permission: 'core.themes.market',
                        url: 'theme/market',
                        subMenus: []
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
                        subMenus: []
                    ),
                    new PackageSubMenuType(
                        title: LangManager::translate('core.menu.package.market'),
                        permission: 'core.packages.market',
                        url: 'packages/market',
                        subMenus: []
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

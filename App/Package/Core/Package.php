<?php

namespace CMW\Package\Core;

use CMW\Manager\Package\IPackageConfig;
use CMW\Manager\Package\PackageMenuType;
use CMW\Manager\Package\PackageSubMenuType;
use CMW\Manager\Theme\ThemeManager;

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
                title: 'Paramètres',
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: 'Réglages du site',
                        permission: 'core.settings.website',
                        url: 'configuration',
                    ),
                    new PackageSubMenuType(
                        title: 'Mode maintenance',
                        permission: 'core.settings.maintenance',
                        url: 'maintenance/manage',
                    ),
                    new PackageSubMenuType(
                        title: 'SMTP et mails',
                        permission: 'core.settings.mails',
                        url: 'mail/configuration',
                    ),
                    new PackageSubMenuType(
                        title: 'Conditions générales',
                        permission: 'core.settings.conditions',
                        url: 'condition',
                    ),
                    new PackageSubMenuType(
                        title: 'Sécurité',
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
                title: 'Mises à jour',
                url: 'updates/cms',
                permission: 'core.update',
                subMenus: [],
            ),
            new PackageMenuType(
                icon: 'fas fa-palette',
                title: 'Thèmes',
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: 'Modifier ' . ThemeManager::getInstance()->getCurrentTheme()->name(),
                        permission: 'core.themes.edit',
                        url: 'theme/manage',
                    ),
                    new PackageSubMenuType(
                        title: 'Thèmes installés',
                        permission: 'core.themes.manage',
                        url: 'theme/theme',
                    ),
                    new PackageSubMenuType(
                        title: 'Parcourir le Market',
                        permission: 'core.themes.market',
                        url: 'theme/market',
                    ),
                ],
            ),
            new PackageMenuType(
                icon: 'fas fa-puzzle-piece',
                title: 'Packages',
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: 'Mes packages',
                        permission: 'core.packages.manage',
                        url: 'packages/package',
                    ),
                    new PackageSubMenuType(
                        title: 'Market',
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

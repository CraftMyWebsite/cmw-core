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
        return "Core";
    }

    public function version(): string
    {
        return "0.0.1";
    }

    public function authors(): array
    {
        return ["CraftMyWebsiteTeam"];
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
                lang: "fr",
                icon: "fas fa-gear",
                title: "Paramètres",
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: 'Réglages du site',
                        permission: 'core.configuration',
                        url: 'configuration',
                    ),
                    new PackageSubMenuType(
                        title: 'Mode maintenance',
                        permission: 'core.maintenance',
                        url: 'maintenance/manage',
                    ),
                    new PackageSubMenuType(
                        title: 'SMTP et mails',
                        permission: 'core.mail.configuration',
                        url: 'mail/configuration',
                    ),
                    new PackageSubMenuType(
                        title: 'Éditeur',
                        permission: 'core.editor.edit',
                        url: 'editor/config',
                    ),
                    new PackageSubMenuType(
                        title: 'Conditions générales',
                        permission: 'core.condition.edit',
                        url: 'condition',
                    ),
                    new PackageSubMenuType(
                        title: 'Sécurité',
                        permission: 'core.security.configuration',
                        url: 'security',
                    ),
                ],
            ),
            new PackageMenuType(
                lang: "fr",
                icon: "fas fa-bars",
                title: "Navigations",
                url: "menus",
                permission: "core.menus.configuration",
                subMenus: [],
            ),
            new PackageMenuType(
                lang: "fr",
                icon: "fas fa-cloud-arrow-down",
                title: "Mises à jour",
                url: "updates/cms",
                permission: "core.update",
                subMenus: [],
            ),
            new PackageMenuType(
                lang: "fr",
                icon: "fas fa-palette",
                title: "Thèmes",
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: 'Modifier ' . ThemeManager::getInstance()->getCurrentTheme()->name(),
                        permission: 'core.theme.configuration',
                        url: 'theme/manage',
                    ),
                    new PackageSubMenuType(
                        title: 'Thèmes installé',
                        permission: 'core.theme.configuration',
                        url: 'theme/theme',
                    ),
                    new PackageSubMenuType(
                        title: 'Parcourir le Market',
                        permission: 'core.theme.configuration',
                        url: 'theme/market',
                    ),
                ],
            ),
            new PackageMenuType(
                lang: "fr",
                icon: "fas fa-puzzle-piece",
                title: "Packages",
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: 'Mes packages',
                        permission: 'core.packages.local',
                        url: 'packages/package',
                    ),
                    new PackageSubMenuType(
                        title: 'Market',
                        permission: 'core.packages.distant',
                        url: 'packages/market',
                    ),
                ],
            ),
            new PackageMenuType(
                lang: "en",
                icon: "fas fa-gear",
                title: "Settings",
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: 'Site Settings',
                        permission: 'core.configuration',
                        url: 'configuration',
                    ),
                    new PackageSubMenuType(
                        title: 'Maintenance mode',
                        permission: 'core.maintenance',
                        url: 'maintenance/manage',
                    ),
                    new PackageSubMenuType(
                        title: 'SMTP and emails',
                        permission: 'core.mail.configuration',
                        url: 'mail/configuration',
                    ),
                    new PackageSubMenuType(
                        title: 'Editor',
                        permission: 'core.editor.edit',
                        url: 'editor/config',
                    ),
                    new PackageSubMenuType(
                        title: 'Terms and conditions',
                        permission: 'core.condition.edit',
                        url: 'condition',
                    ),
                    new PackageSubMenuType(
                        title: 'Security',
                        permission: 'core.security.configuration',
                        url: 'security',
                    ),
                ],
            ),
            new PackageMenuType(
                lang: "en",
                icon: "fas fa-bars",
                title: "Navigations",
                url: "menus",
                permission: "core.menus.configuration",
                subMenus: [],
            ),
            new PackageMenuType(
                lang: "en",
                icon: "fas fa-cloud-arrow-down",
                title: "Updates",
                url: "updates/cms",
                permission: "core.update",
                subMenus: [],
            ),
            new PackageMenuType(
                lang: "en",
                icon: "fas fa-palette",
                title: "Themes",
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: 'Edit ' . ThemeManager::getInstance()->getCurrentTheme()->name(),
                        permission: 'core.theme.configuration',
                        url: 'theme/manage',
                    ),
                    new PackageSubMenuType(
                        title: 'Themes installed',
                        permission: 'core.theme.configuration',
                        url: 'theme/theme',
                    ),
                    new PackageSubMenuType(
                        title: 'Browse the Market',
                        permission: 'core.theme.configuration',
                        url: 'theme/market',
                    ),
                ],
            ),
            new PackageMenuType(
                lang: "en",
                icon: "fas fa-puzzle-piece",
                title: "Packages",
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: 'My packages',
                        permission: 'core.packages.local',
                        url: 'packages/package',
                    ),
                    new PackageSubMenuType(
                        title: 'Browse the Market',
                        permission: 'core.packages.distant',
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
}
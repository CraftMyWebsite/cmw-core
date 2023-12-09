<?php

namespace CMW\Package\Core;

use CMW\Manager\Package\IPackageConfig;
use CMW\Manager\Package\PackageMenuType;
use CMW\Manager\Package\PackageSubMenuType;

class Package implements IPackageConfig
{
    public function name(): string
    {
        return "Core";
    }

    public function version(): string
    {
        return "1.0.0";
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
                icon: "fas fa-th",
                title: "Général",
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: 'Configuration',
                        permission: 'core.configuration',
                        url: 'configuration',
                    ),
                    new PackageSubMenuType(
                        title: 'Configuration éditeur',
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
                    new PackageSubMenuType(
                        title: 'Mises à jour',
                        permission: 'core.update',
                        url: 'update/cms',
                    ),
                ],
            ),
            new PackageMenuType(
                lang: "en",
                icon: "fas fa-th",
                title: "General",
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: 'Configuration',
                        permission: 'core.configuration',
                        url: 'configuration',
                    ),
                    new PackageSubMenuType(
                        title: 'Configuration editor',
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
                    new PackageSubMenuType(
                        title: 'Updates',
                        permission: 'core.update',
                        url: 'update/cms',
                    ),
                ],
            ),
        ];
    }
}
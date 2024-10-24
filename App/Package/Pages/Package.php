<?php

namespace CMW\Package\Pages;

use CMW\Manager\Package\IPackageConfig;
use CMW\Manager\Package\PackageMenuType;

class Package implements IPackageConfig
{
    public function name(): string
    {
        return 'Pages';
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
        return false;
    }

    public function menus(): ?array
    {
        return [
            new PackageMenuType(
                icon: 'fa-solid fa-file-lines',
                title: 'Pages',
                url: 'pages',
                permission: 'pages.show',
                subMenus: []
            ),
        ];
    }

    public function requiredPackages(): array
    {
        return ['Core'];
    }

    /**
     * @return bool
     * @desc <p>Page Package can't be delete.</p>
     */
    public function uninstall(): bool
    {
        // Return true, we don't need other operations for uninstall.
        return false;
    }
}

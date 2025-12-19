<?php

namespace CMW\Package\Pages;

use CMW\Manager\Package\IPackageConfigV2;
use CMW\Manager\Package\PackageMenuType;

class Package implements IPackageConfigV2
{
    public function name(): string
    {
        return 'Pages';
    }

    public function version(): string
    {
        return '1.1.0';
    }

    public function cmwVersion(): string
    {
        return "2.0";
    }

    public function imageLink(): ?string
    {
        return null;
    }

    public function authors(): array
    {
        return ['CraftMyWebsite'];
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
                permission: 'pages.show'
            ),
        ];
    }

    public function compatiblesPackages(): array
    {
        return ["Core"];
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

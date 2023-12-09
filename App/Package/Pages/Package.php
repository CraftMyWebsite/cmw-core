<?php

namespace CMW\Package\Pages;

use CMW\Manager\Package\IPackageConfig;
use CMW\Manager\Package\PackageMenuType;
use CMW\Manager\Package\PackageSubMenuType;

class Package implements IPackageConfig
{
    public function name(): string
    {
        return "Pages";
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
        return false;
    }

    public function menus(): ?array
    {
        return [
            new PackageMenuType(
                lang: "fr",
                icon: "fas fa-copy",
                title: "Pages",
                url: "pages",
                permission: 'pages.show',
                subMenus: []
            ),
            new PackageMenuType(
                lang: "en",
                icon: "fas fa-copy",
                title: "Pages",
                url: 'pages',
                permission: 'pages.show',
                subMenus: [],
            ),
        ];
    }
}
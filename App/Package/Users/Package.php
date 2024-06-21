<?php

namespace CMW\Package\Users;

use CMW\Manager\Package\IPackageConfig;
use CMW\Manager\Package\PackageMenuType;
use CMW\Manager\Package\PackageSubMenuType;

class Package implements IPackageConfig
{
    public function name(): string
    {
        return "Users";
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
                icon: "fas fa-user",
                title: "Utilisateurs",
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: 'Paramètres',
                        permission: 'user.settings',
                        url: 'users/settings',
                    ),
                    new PackageSubMenuType(
                        title: 'Gestion',
                        permission: 'users.settings',
                        url: 'users/manage',
                    ),
                    new PackageSubMenuType(
                        title: 'Rôles',
                        permission: 'users.roles.manage',
                        url: 'roles/manage',
                    ),
                ],
            ),
            new PackageMenuType(
                lang: "en",
                icon: "fas fa-user",
                title: "Users",
                url: null,
                permission: null,
                subMenus: [
                    new PackageSubMenuType(
                        title: 'Settings',
                        permission: 'user.settings',
                        url: 'users/settings',
                    ),
                    new PackageSubMenuType(
                        title: 'Management',
                        permission: 'users.settings',
                        url: 'users/manage',
                    ),
                    new PackageSubMenuType(
                        title: 'Roles',
                        permission: 'users.roles.manage',
                        url: 'roles/manage',
                    ),
                ],
            ),
        ];
    }

    public function requiredPackages(): array
    {
        return ["Core"];
    }

    /**
     * @return bool
     * @desc <p>USers Package can't be delete.</p>
     */
    public function uninstall(): bool
    {
        //Return true, we don't need other operations for uninstall.
        return false;
    }
}
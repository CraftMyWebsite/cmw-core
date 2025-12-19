<?php

namespace CMW\Manager\Package;

interface IPackageConfigV2
{
    /**
     * @return string
     * @desc The package name. (Same as Folder Package Name)
     */
    public function name(): string;

    /**
     * @return string
     * @desc The package version. Never use the precedents known by the market
     */
    public function version(): string;

    /**
     * @return string
     * @desc The compatible CMW (CORE) version (always specify the latest one when updating)
     */
    public function cmwVersion(): string;

    /**
     * @return string|null
     * @desc Return the image link of the theme (optional). Return null if not exist.
     */
    public function imageLink(): ?string;

    /**
     * @return array
     * @desc <p>Ex: ['Teyir', 'Zomb']</p>.
     */
    public function authors(): array;

    /**
     * @return bool
     * @desc Will be classified in the 'GAMES' category in the admin menu
     */
    public function isGame(): bool;

    /**
     * @return bool
     * @desc Return false!
     */
    public function isCore(): bool;

    /**
     * @return \CMW\Manager\Package\PackageMenuType[]|null
     * @desc The auto generated menu in admin
     */
    public function menus(): ?array;

    /**
     * @return array
     * @desc List all the compatibles packages
     */
    public function compatiblesPackages(): array;

    /**
     * @return string[]
     * @desc List all the required packages. For automatic installation
     */
    public function requiredPackages(): array;

    /**
     * @return bool
     * @desc
     * <p>Uninstall package (PHP Requests, clean etc...)</p>
     * <p><b>PLEASE USE Package/$package/Init/uninstall.sql FOR SQL OPERATIONS !!</b></p>
     */
    public function uninstall(): bool;
}

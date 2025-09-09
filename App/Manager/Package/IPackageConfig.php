<?php

namespace CMW\Manager\Package;


/**
 * @deprecated
 * @desc Update your packages quickly will be completely removed in beta-03
 */
interface IPackageConfig
{
    public function name(): string;

    public function version(): string;

    public function authors(): array;

    public function isGame(): bool;

    public function isCore(): bool;

    /**
     * @return \CMW\Manager\Package\PackageMenuType[]|null
     */
    public function menus(): ?array;

    /**
     * @return string[]
     * @desc List all the required packages.
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

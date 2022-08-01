<?php

namespace CMW\Manager\PackageInfo;

interface IPackageInfo
{
    public function getUniqueName(): string;

    public function getDescription(): array;

    public function getAuthor(): Author;

    public function getMenu(): Menu;

    public function getVersion(): string;

    public function getDependencies(): array;

    public function getSoftDependencies(): array;

    public function isCorePackage(): bool;
}

trait NoDependenciesPackageInfo
{
    public function getDependencies(): array
    {
        return array();
    }

    public function getSoftDependencies(): array
    {
        return array();
    }
}

trait DefaultPackageInfo
{
    public function isCorePackage(): bool
    {
        return false;
    }
}
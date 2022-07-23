<?php

namespace CMW\Manager\PackageInfo;

interface IPackageInfo
{
    public function getUniqueName(): string;

    public function getDescription(): array;

    public function getAuthor(): Author;

    public function getMenu(): Menu;

    public function getVersion(): string;

    public function isCorePackage();
}

trait DefaultPackageInfo
{
    public function isCorePackage(): bool
    {
        return false;
    }
}
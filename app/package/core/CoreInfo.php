<?php

use CMW\Manager\PackageInfo\Author;
use CMW\Manager\PackageInfo\DefaultPackageInfo;
use CMW\Manager\PackageInfo\IPackageInfo;
use CMW\Manager\PackageInfo\Menu;

class CoreInfo implements IPackageInfo
{
    use DefaultPackageInfo;

    public function getUniqueName(): string
    {
        return "core";
    }

    public function getDescription(): array
    {
        return array(
            "fr" => "Gère le coeur de CraftMyWebsite",
            "en" => "Manage the base of CraftMyWebsite"
        );
    }

    public function getAuthor(): Author
    {
        return new Author(
            "CraftMyWebsite Team"
        );
    }

    public function getMenu(): Menu
    {
        return new Menu(
            array(
                "fr" => "Général",
                "en" => "General"
            ),
            "fas fa-th",
            "",
            array(
                "configuration" => array(
                    "fr" => "Configuration",
                    "en" => "Configuration"
                )
            )
        );
    }

    public function getVersion(): string
    {
        return "1.0.0";
    }
}
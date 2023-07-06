<?php

namespace CMW\Manager\Class;

class PackageManager
{

    private static function getPackageNameByPathPart(string $packageNameFromPath): ?string
    {
        return match ($packageNameFromPath) {
            "Installation" => "Installer",
            default => $packageNameFromPath
        };
    }

    private static function getElementNameByPathPart(string $elementNameFromPath): ?string
    {

        return match ($elementNameFromPath) {
            "Controllers" => "Controller",
            "Models" => "Model",
            "Entities" => "Entity",
            "Implementations" => "Implementation",
            "Interfaces" => "Interface",
            default => null
        };
    }

    private static function getStartDirFromElementName(string $elementName): ?string
    {
        return match ($elementName) {
            "Controller", "Model", "Entity", "Implementation", "Interface", "Event", "PackageInfo" => "App/Package/",
            "Manager" => "App/Manager/",
            "Utils" => "App/Utils/",
            default => null,
        };
    }

    public static function getClassNamespaceFromPath(string $path): ?string
    {
        $PACKAGE_PREFIX = "CMW";

        $PACKAGE_POSITION = 3;
        $PART_POSITION = 2;
        $CLASSNAME_POSITION = 1;


        $fileParts = explode("\\", $path);
        $package = self::getPackageNameByPathPart($fileParts[count($fileParts) - $PACKAGE_POSITION]);
        $element = self::getElementNameByPathPart($fileParts[count($fileParts) - $PART_POSITION]);
        $className = explode(".php", $fileParts[count($fileParts) - $CLASSNAME_POSITION])[0];

        if ($element === null) {
            return null;
        }

        return "$PACKAGE_PREFIX\\$element\\$package\\$className";
    }

}
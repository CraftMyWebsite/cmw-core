<?php
namespace CMW\Manager\Theme;

use CMW\Controller\Core\PackageController;
use CMW\Manager\Env\EnvManager;

class ThemeMapper
{
    public static function mapConfigKey(string $menuKey, string $themeKey): string
    {
        return $menuKey . '_' . $themeKey;
    }
}

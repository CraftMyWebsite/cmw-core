<?php
namespace CMW\Manager\Theme\Config;

use CMW\Controller\Core\PackageController;
use CMW\Manager\Env\EnvManager;

class ThemeSettingsMapper
{
    public static function getFlattened(string $themeName): array
    {
        $configPath = EnvManager::getInstance()->getValue('DIR') . "Public/Themes/$themeName/Config/config.settings.php";

        if (!file_exists($configPath)) {
            return [];
        }

        $menus = include $configPath;
        $flat = [];

        foreach ($menus as $menu) {
            if (isset($menu->requiredPackage) && !PackageController::isInstalled($menu->requiredPackage)) {
                continue;
            }

            foreach ($menu->values as $value) {
                $key = ThemeMapper::mapConfigKey($menu->key, $value->themeKey);
                $flat[$key] = $value->defaultValue;
            }
        }

        return $flat;
    }
}

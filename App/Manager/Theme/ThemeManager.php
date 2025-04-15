<?php

namespace CMW\Manager\Theme;

use CMW\Manager\Manager\AbstractManager;
use CMW\Manager\Theme\Loader\ThemeLoader;

class ThemeManager extends AbstractManager
{
    public static string $defaultThemeName = "Sampler";









    /*
     * deprecated and unused method waiting for deletion
     * */

    /**
     * @return array
     * @deprecated Sera supprimé en alpha-10 remplacé par : getFlattenedThemeConfigSettings
     */
    public function getCurrentThemeConfigSettings(): array
    {
        $themeConfigFile = 'Public/Themes/' . ThemeLoader::getInstance()->getCurrentTheme()->name() . '/Config/config.settings.php';

        if (!file_exists($themeConfigFile)) {
            return [];
        }

        $content = include $themeConfigFile;

        if (!is_array($content)) {
            return [];
        }

        return $content;
    }

    /**
     * @param string $setting
     * @return ?string
     * @Desc Return a specific local setting
     * @deprecated Sera supprimé en alpha-10. La methode est utilisée dans des méthodes dépréciées
     */
    public function getCurrentThemeConfigSetting(string $setting): ?string
    {
        return $this->getCurrentThemeConfigSettings()[$setting] ?? null;
    }
}

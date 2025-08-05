<?php
namespace CMW\Manager\Theme\Config;

class ThemeMapper
{
    public static function mapConfigKey(string $menuKey, string $themeKey): string
    {
        return $menuKey . '_' . $themeKey;
    }
}

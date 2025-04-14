<?php

namespace CMW\Manager\Theme;

use CMW\Controller\Core\PackageController;
use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Cache\SimpleCacheManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Manager\AbstractManager;
use CMW\Manager\Theme\Editor\EditorMenu;
use CMW\Manager\Theme\Editor\EditorRangeOptions;
use CMW\Manager\Theme\Editor\EditorType;
use CMW\Manager\Theme\Editor\EditorValue;
use CMW\Manager\Theme\Exceptions\ThemeNotFoundException;
use CMW\Model\Core\CoreModel;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\Directory;

class ThemeManager extends AbstractManager
{
    public static string $defaultThemeName = "Sampler";

    public function defaultImageLink(): string
    {
        return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Admin/Resources/Assets/Img/local-theme.jpg';
    }

    public function getCurrentTheme(): IThemeConfig
    {
        $currentThemeName = self::$defaultThemeName;
        $isInstallation = EnvManager::getInstance()->getValue('INSTALLSTEP') !== '-1';

        if (!$isInstallation) {
            $currentThemeName = CoreModel::getInstance()->fetchOption('Theme');
        }

        if (!$this->isLocalThemeExist($currentThemeName)) {
            (new ThemeNotFoundException($currentThemeName))->invokeErrorPage();
        }

        return $this->getTheme($currentThemeName);
    }

    /**
     * @param string $themeName
     * @return IThemeConfig|null
     */
    public function getTheme(string $themeName): ?IThemeConfig
    {
        $namespace = 'CMW\\Theme\\' . $themeName . '\Theme';

        if (!class_exists($namespace)) {
            return null;
        }

        $classInstance = new $namespace();

        if (!is_subclass_of($classInstance, IThemeConfig::class)) {
            return null;
        }

        return $classInstance;
    }

    /**
     * @return IThemeConfig[]
     */
    public function getInstalledThemes(): array
    {
        $toReturn = [];
        $themesFolder = 'Public/Themes';
        $contentDirectory = array_diff(scandir("$themesFolder/"), ['..', '.']);
        foreach ($contentDirectory as $theme) {
            if (file_exists("$themesFolder/$theme/Theme.php") && !empty(file_get_contents("$themesFolder/$theme/Theme.php"))) {
                $toReturn[] = $this->getTheme($theme);
            }
        }

        return $toReturn;
    }

    /**
     * @return IThemeConfig[]
     * @desc Return all themes local (remove thème get from the public market)
     */
    public function getLocalThemes(): array
    {
        $toReturn = [];
        $installedThemes = $this->getInstalledThemes();

        $marketThemesName = [];

        foreach ($this->getMarketThemes() as $marketTheme):
            $marketThemesName[] = $marketTheme['name'];
        endforeach;

        foreach ($installedThemes as $installedTheme):
            if (!in_array($installedTheme->name(), $marketThemesName, true)):
                $toReturn[] = $installedTheme;
            endif;
        endforeach;

        return $toReturn;
    }

    /**
     * @return void
     */
    public function getCurrentThemeConfigFile(): void
    {
        $themeConfigFile = 'Public/Themes/' . $this->getCurrentTheme()->name() . '/Config/config.php';
        require_once $themeConfigFile;
    }

    /**
     * @return array
     * @deprecated Sera supprimé en alpha-10 remplacé par : getFlattenedThemeConfigSettings
     */
    public function getCurrentThemeConfigSettings(): array
    {
        $themeConfigFile = 'Public/Themes/' . $this->getCurrentTheme()->name() . '/Config/config.settings.php';

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

    /**
     * @param string $theme
     * @return bool
     */
    public function isThemeInstalled(string $theme): bool
    {
        foreach ($this->getInstalledThemes() as $installedTheme) {
            if ($theme === $installedTheme->name()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $theme
     * @desc Apply the settings of a theme and overwrite existing values.
     * @return void
     */
    public function installThemeSettings(string $theme): void
    {
        $themeConfigFile = "Public/Themes/$theme/Config/config.settings.php";

        if (!file_exists($themeConfigFile)) {
            return;
        }

        $menus = include $themeConfigFile;

        foreach ($menus as $menu) {
            if (!($menu instanceof EditorMenu)) {
                continue;
            }

            foreach ($menu->values as $value) {
                if (!($value instanceof EditorValue)) {
                    continue;
                }

                $configKey = ThemeMapper::mapConfigKey($menu->key, $value->themeKey);
                $defaultValue = $value->defaultValue;

                ThemeModel::getInstance()->storeThemeConfig($configKey, $defaultValue, $theme);
            }
        }
    }

    /**
     * @param string $theme
     * @desc Update the settings of a theme without overwriting existing values.
     * @return void
     */
    public function updateThemeSettings(string $theme): void
    {
        $themeConfigFile = "Public/Themes/$theme/Config/config.settings.php";

        if (!file_exists($themeConfigFile)) {
            return;
        }

        $menus = include $themeConfigFile;

        $existingKeys = ThemeModel::getInstance()->getExistingThemeConfigKeys($theme);


        foreach ($menus as $menu) {
            if (!($menu instanceof EditorMenu)) {
                continue;
            }

            foreach ($menu->values as $value) {
                if (!($value instanceof EditorValue)) {
                    continue;
                }

                $configKey = ThemeMapper::mapConfigKey($menu->key, $value->themeKey);

                $newConfigs = [];

                if (!in_array($configKey, $existingKeys, true)) {
                    $newConfigs[] = [
                        'name' => $configKey,
                        'value' => $value->defaultValue
                    ];
                }
            }
        }
        if (!empty($newConfigs)) {
            ThemeModel::getInstance()->storeThemeConfigBulk($newConfigs, $theme);
        }
    }

    /**
     * @return array
     * @desc Return the list of public thèmes from our market
     */
    public function getMarketThemes(): array
    {
        return PublicAPI::getData('market/resources/filtered/0');
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isLocalThemeExist(string $name): bool
    {
        return file_exists("Public/Themes/$name/Theme.php");
    }

    /**
     * @param string $name
     * @return UninstallThemeType
     * @desc Completely uninstall a local theme (delete files and database)
     */
    public function uninstallLocalTheme(string $name): UninstallThemeType
    {
        if (!$this->isLocalThemeExist($name)) {
            return UninstallThemeType::ERROR_THEME_NOT_FOUND;
        }

        //Prevent default theme uninstallation
        if ($name === self::$defaultThemeName) {
            return UninstallThemeType::ERROR_THEME_IS_DEFAULT;
        }

        // Uninstall DB
        $configPdo = ThemeModel::getInstance()->getInstance()->transactionalDeleteThemeConfig($name);

        // Uninstall files
        if (!Directory::delete(EnvManager::getInstance()->getValue('DIR') . "Public/Themes/$name")) {
            $configPdo->rollBack();
            return UninstallThemeType::ERROR_THEME_DELETE_FILES;
        }

        //If all is good, we commit the transaction
        $configPdo->commit();

        return UninstallThemeType::SUCCESS;
    }

    public function getConfigValueFromCache(string $themeName, string $themeConfigNameFormatted, string $menuKey, string $themeKey, ?string $type): ?string
    {
        if (!SimpleCacheManager::cacheExist('config', "Themes/$themeName")) {
            return null;
        }

        $data = SimpleCacheManager::getCache('config', "Themes/$themeName");

        foreach ($data as $conf) {
            if ($conf['theme_config_name'] === $themeConfigNameFormatted) {
                if ($type === EditorType::IMAGE) {
                    $default = $this->getDefaultThemeValue($menuKey, $themeKey);
                    if (!$conf['theme_config_value'] || $conf['theme_config_value'] === $default) {
                        return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . "Public/Themes/{$themeName}/{$default}";
                    }
                    return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . "Public/Uploads/{$themeName}/Img/{$conf['theme_config_value']}";
                }
                return $conf['theme_config_value'] ?? "UNDEFINED_$themeConfigNameFormatted";
            }
        }

        return null;
    }

    /**
     * Récupère la valeur par défaut d'une clé de thème depuis le fichier de configuration.
     *
     * @param string $key
     * @return mixed|null
     */
    public function getDefaultThemeValue(string $MenuKey, string $key) : string
    {
        $themeName = ThemeManager::getInstance()->getCurrentTheme()->name();
        $configPath = EnvManager::getInstance()->getValue('DIR') . "Public/Themes/{$themeName}/Config/config.settings.php";

        if (!file_exists($configPath)) {
            return '';
        }

        $menus = include $configPath;

        foreach ($menus as $menu) {
            if (isset($menu->requiredPackage) && !PackageController::isInstalled($menu->requiredPackage)) {
                continue;
            }
            if ($menu->key === $MenuKey) {
                foreach ($menu->values as $value) {
                    if ($value->themeKey === $key) {
                        return $value->defaultValue;
                    }
                }
            }

        }

        return '';
    }

    public function resolveImagePath(string $themeName, ?string $value, string $menuKey, string $themeKey): string
    {
        $default = $this->getDefaultThemeValue($menuKey, $themeKey);
        $subfolder = EnvManager::getInstance()->getValue('PATH_SUBFOLDER');

        if (!$value || $value === $default) {
            return $subfolder . "Public/Themes/{$themeName}/{$default}";
        }

        return $subfolder . "Public/Uploads/{$themeName}/Img/{$value}";
    }



    /*--EDITOR--*/

    /**
     * @return EditorMenu[]
     */
    public function getThemeMenus(): array {
        $themeName = ThemeManager::getInstance()->getCurrentTheme()->name();

        $configPath = EnvManager::getInstance()->getValue('DIR') . "Public/Themes/$themeName/Config/config.settings.php";

        if (!file_exists($configPath)) {
            return [];
        }

        $menus = include $configPath;

        return array_filter($menus, function ($menu) {
            return !isset($menu->requiredPackage) || PackageController::isInstalled($menu->requiredPackage);
        });
    }

    /**
     * @param string $menuKey
     * @param string $themeKey
     * @return string|null
     */
    public function getEditorType(string $menuKey, string $themeKey): ?string
    {
        foreach ($this->getThemeMenus() as $menu) {
            if ($menu->getMenuKey() === $menuKey) {
                foreach ($menu->values as $value) {
                    if ($value->themeKey === $themeKey) {
                        return $value->type;
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param string $menuKey
     * @param string $themeKey
     * @return EditorRangeOptions|null
     */
    public function getEditorRangeOptions(string $menuKey, string $themeKey): ?EditorRangeOptions
    {

        foreach ($this->getThemeMenus() as $menu) {
            if ($menu->key !== $menuKey) continue;
            foreach ($menu->values as $val) {
                if ($val->themeKey === $themeKey && isset($val->rangeOptions[0])) {
                    return $val->rangeOptions[0];
                }
            }
        }
        return null;
    }

    public function replaceThemeValues(string $html, bool $editorMode = false): string
    {
        if ($editorMode) {
            return $html;
        }

        // data-cmw="menu:key"
        $html = preg_replace_callback('/<([a-z0-9]+)([^>]*)data-cmw="([\w-]+):([\w-]+)"([^>]*)>(.*?)<\/\1>/si', function ($m) {
            $tag = $m[1];
            $before = $m[2];
            $menu = $m[3];
            $key = $m[4];
            $after = $m[5];

            $value = ThemeModel::getInstance()->fetchConfigValue($menu, $key);

            return "<{$tag}{$before}{$after}>{$value}</{$tag}>";
        }, $html);


        // data-cmw-style="prop:menu:key[;...]"
        $html = preg_replace_callback('/<([a-z0-9]+)([^>]*)data-cmw-style="([^"]+)"([^>]*)>/i', function ($m) {
            $tag = $m[1];
            $before = $m[2];
            $cmwAttr = $m[3];
            $after = $m[4];

            preg_match('/style="([^"]*)"/i', $before . $after, $existingStyleMatch);
            $existingStyles = [];

            if (isset($existingStyleMatch[1])) {
                foreach (explode(';', $existingStyleMatch[1]) as $styleLine) {
                    if (strpos($styleLine, ':') !== false) {
                        [$k, $v] = explode(':', $styleLine, 2);
                        $existingStyles[trim($k)] = trim($v);
                    }
                }
            }

            $styles = explode(';', $cmwAttr);
            foreach ($styles as $entry) {
                [$prop, $menu, $key] = explode(':', $entry);
                $val = ThemeModel::getInstance()->fetchConfigValue($menu, $key);
                $editorType = ThemeManager::getInstance()->getEditorType($menu, $key);

                if ($editorType === EditorType::RANGE) {
                    $options = ThemeManager::getInstance()->getEditorRangeOptions($menu, $key);
                    if ($options instanceof EditorRangeOptions) {
                        $val = $options->getPrefix() . $val . $options->getSuffix();
                    }
                }

                // pour les images utilisées dans des styles CSS
                $imageStyleProps = ['background', 'background-image', 'list-style-image', 'mask-image'];
                if ($editorType === EditorType::IMAGE && in_array(trim($prop), $imageStyleProps)) {
                    $val = "url('{$val}')";
                }

                $existingStyles[trim($prop)] = $val;
            }

            $cleaned = preg_replace('/style="[^"]*"/i', '', $before . $after);
            $finalStyle = implode('; ', array_map(fn($k, $v) => "$k: $v", array_keys($existingStyles), $existingStyles));

            return "<{$tag} {$cleaned}style=\"{$finalStyle}\">";
        }, $html);



        // data-cmw-class="menu:key [...]"
        $html = preg_replace_callback('/<([a-z0-9]+)([^>]*)data-cmw-class="([^"]+)"([^>]*)>/i', function ($m) {
            $tag = $m[1];
            $before = $m[2];
            $cmwAttr = $m[3];
            $after = $m[4];

            // Récupérer les classes déjà présentes (dans before ou after)
            preg_match('/class="([^"]*)"/i', $before . $after, $existingClassMatch);
            $existingClasses = isset($existingClassMatch[1]) ? explode(' ', $existingClassMatch[1]) : [];

            $refs = explode(' ', $cmwAttr);
            $dynamicClasses = [];

            foreach ($refs as $ref) {
                [$menu, $key] = explode(':', $ref);
                $val = ThemeModel::getInstance()->fetchConfigValue($menu, $key);
                $editorType = ThemeManager::getInstance()->getEditorType($menu, $key);

                if ($editorType === EditorType::RANGE) {
                    $options = ThemeManager::getInstance()->getEditorRangeOptions($menu, $key);
                    if ($options instanceof EditorRangeOptions) {
                        $val = $options->getPrefix() . $val . $options->getSuffix();
                    }
                }

                if ($val) {
                    $dynamicClasses[] = $val;
                }
            }

            // Supprimer l'ancien class="..." du before et after
            $cleaned = preg_replace('/class="[^"]*"/i', '', $before . $after);

            // Fusion et reconstruction
            $finalClasses = array_filter(array_merge($existingClasses, $dynamicClasses));
            return "<{$tag} {$cleaned}class=\"" . implode(' ', $finalClasses) . "\">";
        }, $html);


        // data-cmw-visible="menu:key" → suppression de l’élément si valeur = 0
        $html = preg_replace_callback('/<([a-z]+)([^>]+)data-cmw-visible="([\w-]+):([\w-]+)"([^>]*)>(.*?)<\/\1>/si', function ($m) {
            $visible = ThemeModel::getInstance()->fetchConfigValue($m[3], $m[4]);
            if (!$visible || $visible === '0') {
                return ''; // supprimer l’élément entier
            }

            return "<{$m[1]}{$m[2]}{$m[5]}>{$m[6]}</{$m[1]}>";
        }, $html);

        // data-cmw-attr="attr:menu:key [...]"
        $html = preg_replace_callback('/data-cmw-attr="([^"]+)"/', function ($m) {
            $defs = explode(' ', $m[1]);
            $attrs = [];

            foreach ($defs as $def) {
                [$attr, $menu, $key] = explode(':', $def);
                $val = ThemeModel::getInstance()->fetchConfigValue($menu, $key);

                $attrs[] = "{$attr}=\"{$val}\"";
            }

            return implode(' ', $attrs);
        }, $html);

        return $html;
    }
}

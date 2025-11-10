<?php

namespace CMW\Manager\Theme\Loader;

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Manager\AbstractManager;
use CMW\Manager\Notice\WarningManager;
use CMW\Manager\Theme\Exceptions\ThemeNotFoundException;
use CMW\Manager\Theme\IThemeConfig;
use CMW\Manager\Theme\IThemeConfigV2;
use CMW\Manager\Theme\Adapter\LegacyThemeAdapter;
use CMW\Manager\Theme\ThemeManager;
use CMW\Model\Core\CoreModel;
use CMW\Utils\Directory;

class ThemeLoader extends AbstractManager
{
    private static ?array $ignoredThemesCache = null;

    private const FALLBACK_THEME = 'Sampler';

    public function getCurrentTheme(): IThemeConfigV2
    {
        $currentThemeName = ThemeManager::$defaultThemeName;
        $isInstallation = EnvManager::getInstance()->getValue('INSTALLSTEP') !== '-1';

        if (!$isInstallation) {
            $currentThemeName = CoreModel::getInstance()->fetchOption('theme');
        }

        if (self::isDisabled($currentThemeName) || !$this::getInstance()->isLocalThemeExist($currentThemeName)) {
            $currentThemeName = self::FALLBACK_THEME;
        }

        if (!$this::getInstance()->isLocalThemeExist($currentThemeName)) {
            (new ThemeNotFoundException($currentThemeName))->invokeErrorPage();
        }

        return $this::getInstance()->getTheme($currentThemeName);
    }

    /**
     * @param string $themeName
     * @return IThemeConfigV2|null
     */
    public function getTheme(string $themeName): ?IThemeConfigV2
    {
        $namespace = 'CMW\\Theme\\' . $themeName . '\Theme';

        if (!class_exists($namespace)) {
            return null;
        }

        $classInstance = new $namespace();

        if ($classInstance instanceof IThemeConfigV2) {
            return $classInstance;
        }

        if ($classInstance instanceof IThemeConfig) {
            WarningManager::addError("Le thème <b>{$themeName}</b> utilise l'ancienne interface <code>IThemeConfig</code>. Migre vers <code>IThemeConfigV2</code> ou mets à jour le thème pour rester compatible.<br><code>IThemeConfig</code> sera supprimé en beta-03.");
            error_log("[CMW] Theme '$themeName' uses IThemeConfig (deprecated, removed in beta-03). Migrate to IThemeConfigV2 or update your theme.");
            return new LegacyThemeAdapter($classInstance);
        }

        return null;
    }

    /**
     * @return IThemeConfigV2[]
     */
    public function getInstalledThemes(): array
    {
        $toReturn = [];
        $themesFolder = 'Public/Themes';
        $themeDirs = Directory::getFolders($themesFolder);

        foreach ($themeDirs as $theme) {
            if (self::isDisabled($theme)) {
                continue;
            }

            $themeFile = "$themesFolder/$theme/Theme.php";
            if (file_exists($themeFile) && !empty(file_get_contents($themeFile))) {
                $toReturn[] = $this->getTheme($theme);
            }
        }

        return $toReturn;
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
     * @param string $name
     * @return bool
     */
    public function isLocalThemeExist(string $name): bool
    {
        return file_exists("Public/Themes/$name/Theme.php");
    }

    public static function getIgnoredThemes(): array
    {
        if (self::$ignoredThemesCache !== null) {
            return self::$ignoredThemesCache;
        }

        $ignored = [];

        $envValue = EnvManager::getInstance()->getValue('DISABLED_THEME');
        if ($envValue) {
            $envIgnored = array_map('trim', explode(',', $envValue));

            foreach ($envIgnored as $themeName) {
                if (strcasecmp($themeName, self::FALLBACK_THEME) === 0) {
                    continue;
                }

                error_log("[CMW] Theme '$themeName' USER RUN THEME BUT API CMW NOT ALLOW THIS ON THIS DOMAIN : {$_SERVER['SERVER_NAME']}, SUPPORT D'ONT HELP THIS USER AND NOTIFY ADMIN QUICKLY !");
                WarningManager::addError("Le thème <b>{$themeName}</b> a été desactiver. CraftMyWebsite a remarqué que vous tentez d'installer un thème payant en contournant le système de vérification.<br>Votre domaine <b>{$_SERVER['SERVER_NAME']}</b> n'est pas autorisé pour faire fonctionner <b>{$themeName}</b>.<br>Veuillez corriger cela rapidement sous peine de prendre des sanctions (blacklistage de votre site sur l'api de craftmywebsite.fr)");
            }

            $ignored = $envIgnored;
        }

        $path = EnvManager::getInstance()->getValue('DIR') . DIRECTORY_SEPARATOR . '.ignored_themes';
        if (is_file($path) && is_readable($path)) {
            $file = file_get_contents($path);
            if ($file) {
                $lines = array_map('trim', explode("\n", $file));
                $ignored = array_merge($ignored, $lines);
            }
        }

        $ignored = array_filter($ignored, static fn($t) => strcasecmp($t, self::FALLBACK_THEME) !== 0);

        self::$ignoredThemesCache = array_unique(array_filter($ignored));

        return self::$ignoredThemesCache;
    }

private static function isDisabled(string $theme): bool
    {
        foreach (self::getIgnoredThemes() as $ignored) {
            if (strcasecmp($ignored, $theme) === 0) {
                return true;
            }
        }
        return false;
    }

}
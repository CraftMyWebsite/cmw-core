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
    public function getCurrentTheme(): IThemeConfigV2
    {
        $currentThemeName = ThemeManager::$defaultThemeName;
        $isInstallation = EnvManager::getInstance()->getValue('INSTALLSTEP') !== '-1';

        if (!$isInstallation) {
            $currentThemeName = CoreModel::getInstance()->fetchOption('theme');
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

}
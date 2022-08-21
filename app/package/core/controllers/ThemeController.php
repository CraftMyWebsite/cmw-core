<?php

namespace CMW\Controller\Core;

use CMW\Entity\Core\ThemeEntity;
use CMW\Model\Core\CoreModel;
use CMW\Router\Link;
use CMW\Utils\Redirect;
use CMW\Utils\Response;
use CMW\Utils\View;
use JsonException;

class ThemeController extends CoreController
{

    /* THEME FUNCTIONS */

    public static function getInstalledThemes(): array
    {
        $toReturn = array();
        $themesFolder = 'public/themes';
        $contentDirectory = array_diff(scandir("$themesFolder/"), array('..', '.'));
        foreach ($contentDirectory as $theme) {
            $toReturn[] = self::getTheme($theme);
        }

        return $toReturn;
    }

    public static function getTheme(string $themeName): ?ThemeEntity
    {

        try {
            $strJsonFileContents = file_get_contents("public/themes/$themeName/infos.json");
            $themeInfos = json_decode($strJsonFileContents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            return null;
        }

        return new ThemeEntity(
            $themeInfos['name'] ?? "",
            $themeInfos['creator'] ?? "",
            $themeInfos['version'] ?? "",
            $themeInfos['cmwVersion'] ?? "",
            $themeInfos['packages'] ?? ""
        );
    }

    /**
     * @throws JsonException
     */
    public function cmwPackageAvailableTheme(string $package): bool
    {
        return in_array($package, $this->cmwThemeAvailablePackages(), true);
    }

    /**
     * @throws JsonException
     */
    public function cmwThemeAvailablePackages(): array
    {
        $jsonFile = file_get_contents(self::getCurrentTheme()->getName() . "/infos.json");
        return json_decode($jsonFile, true, 512, JSON_THROW_ON_ERROR)["packages"];
    }

    public static function getCurrentTheme(): ThemeEntity
    {
        $currentThemeName = (new CoreModel())->fetchOption("theme");

        return self::getTheme($currentThemeName);
    }

    /* ADMINISTRATION */

    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin/theme")]
    #[Link("/configuration", Link::GET, [], "/cmw-admin/theme")]
    public function adminThemeConfiguration(): void
    {
        $currentTheme = self::getCurrentTheme();
        $installedThemes = self::getInstalledThemes();

        View::createAdminView("core", "themeConfiguration")
            ->addVariableList(["currentTheme" => $currentTheme, "installedThemes" => $installedThemes])
            ->view();
    }

    #[Link("/configuration", Link::POST, [], "/cmw-admin/theme")]
    public function adminThemeConfigurationPost(): void
    {
        $theme = filter_input(INPUT_POST, "theme");

        CoreModel::updateOption("theme", $theme);
        header("Location: configuration");
    }



}

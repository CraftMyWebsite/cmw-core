<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Entity\Core\ThemeEntity;
use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Download\DownloadManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Router\Link;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Manager\Views\View;
use CMW\Model\Core\CoreModel;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\Redirect;
use JsonException;

class ThemeController extends AbstractController
{

    /* THEME FUNCTIONS */

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
        $currentThemeName = CoreModel::getInstance()->fetchOption("Theme");

        return self::getTheme($currentThemeName);
    }

    public static function getTheme(string $themeName): ?ThemeEntity
    {

        try {
            $strJsonFileContents = file_get_contents("Public/Themes/$themeName/infos.json");
            $themeInfos = json_decode($strJsonFileContents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return null;
        }

        return new ThemeEntity(
            $themeInfos['name'] ?? "",
            $themeInfos['author'] ?? "",
            $themeInfos['authors'] ?? [],
            $themeInfos['version'] ?? "",
            $themeInfos['cmwVersion'] ?? "",
            $themeInfos['packages'] ?? []
        );
    }

    /**
     * @return ThemeEntity[]
     */
    public static function getInstalledThemes(): array
    {
        $toReturn = array();
        $themesFolder = 'Public/Themes';
        $contentDirectory = array_diff(scandir("$themesFolder/"), array('..', '.'));
        foreach ($contentDirectory as $theme) {
            if (file_exists("$themesFolder/$theme/infos.json") && !empty(file_get_contents("$themesFolder/$theme/infos.json"))) {
                $toReturn[] = self::getTheme($theme);
            }
        }

        return $toReturn;
    }

    /**
     * @return ThemeEntity[]
     * @desc Return all themes local (remove thème get from the public market)
     */
    public static function getLocalThemes(): array
    {
        $toReturn = array();
        $installedThemes = self::getInstalledThemes();

        $marketThemesName = array();

        foreach (self::getMarketThemes() as $marketTheme):
            $marketThemesName[] = $marketTheme['name'];
        endforeach;

        foreach ($installedThemes as $installedTheme):
            if (!in_array($installedTheme->getName(), $marketThemesName, true)):
                $toReturn[] = $installedTheme;
            endif;
        endforeach;

        return $toReturn;
    }

    public static function getCurrentThemeConfigFile(): void
    {
        $themeConfigFile = "Public/Themes/" . self::getCurrentTheme()->getName() . "/Config/config.php";
        require_once $themeConfigFile;
    }

    private function getCurrentThemeConfigSettings(): array
    {
        $themeConfigFile = "Public/Themes/" . self::getCurrentTheme()->getName() . "/Config/config.settings.php";

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
     */
    public function getCurrentThemeConfigSetting(string $setting): ?string
    {

        return $this->getCurrentThemeConfigSettings()[$setting] ?? null;
    }

    /**
     * @param string $theme
     * @return bool
     */
    public static function isThemeInstalled(string $theme): bool
    {
        foreach (self::getInstalledThemes() as $installedTheme) {
            if ($theme === $installedTheme->getName()){
                return true;
            }
        }

        return false;
    }

    public function installThemeSettings(string $theme): void
    {
        $themeConfigFile = "Public/Themes/$theme/Config/config.settings.php";

        if (!file_exists($themeConfigFile)) {
            return;
        }

        $content = include $themeConfigFile;


        foreach ($content as $config => $value) {
            ThemeModel::getInstance()->storeThemeConfig($config, $value, $theme);
        }
    }

    /**
     * @return array
     * @desc Return the list of public thèmes from our market
     */
    public static function getMarketThemes(): array {
        return PublicAPI::getData("resources/getResources&resource_type=0");
    }

    /* ADMINISTRATION */

    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin/theme")]
    #[Link("/market", Link::GET, [], "/cmw-admin/theme")]
    private function adminThemeConfiguration(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.Theme.configuration");

        $currentTheme = self::getCurrentTheme();
        $installedThemes = self::getInstalledThemes();
        $themesList = self::getMarketThemes();
        
        View::createAdminView("Core", "themeMarket")
            ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css", "Admin/Resources/Assets/Css/Pages/simple-datatables.css")
            ->addVariableList(["currentTheme" => $currentTheme, "installedThemes" => $installedThemes, "themesList" => $themesList])
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/Umd/simple-datatables.js", "Admin/Resources/Assets/Js/Pages/simple-datatables.js")
            ->view();
    }

    #[Link("/market", Link::POST, [], "/cmw-admin/theme")]
    private function adminThemeConfigurationPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.Theme.configuration");

        $theme = filter_input(INPUT_POST, "theme");

        CoreModel::updateOption("theme", $theme);

        Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate("core.toaster.config.success"));

        header("Location: market");
    }

    #[Link("/market/regenerate", Link::POST, [], "/cmw-admin/theme")]
    private function adminThemeConfigurationRegeneratePost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.Theme.configuration");

        $themeName = self::getCurrentTheme()->getName();
        ThemeModel::getInstance()->deleteThemeConfig($themeName);
        $this->installThemeSettings($themeName);

        Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate("core.toaster.Theme.regenerate"));

        header("Location: ../market");
    }

    #[Link("/install/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/theme")]
    private function adminThemeInstallation(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.Theme.configuration");

        $theme = PublicAPI::getData("resources/installResource&id=$id");

        if (!DownloadManager::installPackageWithLink($theme['file'], "Theme", $theme['name'])){
            Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                LangManager::translate("core.downloads.errors.internalError",
                    ['name' => $theme['name'], 'version' => $theme['version_name']]));
            Redirect::redirectPreviousRoute();
            return;
        }

        //Install Theme settings
        $this->installThemeSettings($theme['name']);
        CoreModel::updateOption("theme", $theme['name']);

        //TODO TOASTER

        Redirect::redirectPreviousRoute();
    }


    #[Link("/manage", Link::GET, [], "/cmw-admin/theme")]
    private function adminThemeManage(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.Theme.configuration");
        View::createAdminView("Core", "themeManage")
            ->addScriptBefore("Admin/Resources/Vendors/Tinymce/tinymce.min.js","Admin/Resources/Vendors/Tinymce/Config/full.js")
            ->view();
    }

    #[Link("/manage", Link::POST, [], "/cmw-admin/theme")]
    private function adminThemeManagePost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.Theme.configuration");

        $aresFiles = [];

        // Manage files
        foreach ($_FILES as $conf => $file) {
            $aresFiles['__images__'][$conf] = true;

            //If file is empty, we don't update the config.
            if ($file['name'] !== "") {

                $imageName = ImagesManager::upload($file, self::getCurrentTheme()->getName() . "/Img");
                if (!str_contains($imageName, "ERROR")) {
                    $remoteImageValue = ThemeModel::fetchConfigValue($conf);
                    $localImageValue = (new ThemeController())->getCurrentThemeConfigSetting($conf);

                    if ($remoteImageValue !== $file && $remoteImageValue !== $localImageValue) {
                        ImagesManager::deleteImage(self::getCurrentTheme()->getName() . "/Img/$remoteImageValue");
                    }

                    ThemeModel::getInstance()->updateThemeConfig($conf, $imageName, self::getCurrentTheme()->getName());
                } else {
                    Flash::send("error", LangManager::translate("core.toaster.error"),
                        $conf . " => " . $imageName);
                }
            }
        }


        // Manage inputs
        foreach ($this->getCurrentThemeConfigSettings() as $conf => $value) {
            if (isset($aresFiles['__images__'][$conf])) {
                continue;
            }

            if (!isset($_POST[$conf]) || !empty($_POST[$conf])) {
                ThemeModel::getInstance()->updateThemeConfig($conf, $_POST[$conf] ?? "0", self::getCurrentTheme()->getName());
            }
        }

        Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate("core.toaster.config.success"));

        header("location: manage");
    }

}

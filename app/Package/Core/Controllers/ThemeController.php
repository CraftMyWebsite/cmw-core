<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Entity\Core\ThemeEntity;
use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Download\DownloadManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Manager\Views\View;
use CMW\Model\Core\CoreModel;
use CMW\Model\Core\ThemeModel;
use CMW\Router\Link;
use CMW\Utils\Redirect;
use CMW\Utils\Response;
use CMW\Utils\Utils;
use JsonException;
use ZipArchive;

class ThemeController extends CoreController
{

    private ThemeModel $themeModel;


    public function __construct()
    {
        parent::__construct();

        $this->themeModel = new ThemeModel();
    }

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
        $currentThemeName = (new CoreModel())->fetchOption("theme");

        return self::getTheme($currentThemeName);
    }

    public static function getTheme(string $themeName): ?ThemeEntity
    {

        try {
            $strJsonFileContents = file_get_contents("public/themes/$themeName/infos.json");
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

    public static function getInstalledThemes(): array
    {
        $toReturn = array();
        $themesFolder = 'public/themes';
        $contentDirectory = array_diff(scandir("$themesFolder/"), array('..', '.'));
        foreach ($contentDirectory as $theme) {
            if (file_exists("$themesFolder/$theme/infos.json") && !empty(file_get_contents("$themesFolder/$theme/infos.json"))) {
                $toReturn[] = self::getTheme($theme);
            }
        }

        return $toReturn;
    }

    public static function getCurrentThemeConfigFile(): void
    {
        $themeConfigFile = "public/themes/" . self::getCurrentTheme()->getName() . "/config/config.php";
        require_once $themeConfigFile;
    }

    private function getCurrentThemeConfigSettings(): array
    {
        $themeConfigFile = "public/themes/" . self::getCurrentTheme()->getName() . "/config/config.settings.php";

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
        $themeConfigFile = "public/themes/$theme/config/config.settings.php";

        if (!file_exists($themeConfigFile)) {
            return;
        }

        $content = include $themeConfigFile;


        foreach ($content as $config => $value) {
            $this->themeModel->storeThemeConfig($config, $value, $theme);
        }
    }


    /* ADMINISTRATION */

    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin/theme")]
    #[Link("/configuration", Link::GET, [], "/cmw-admin/theme")]
    public function adminThemeConfiguration(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.theme.configuration");

        $currentTheme = self::getCurrentTheme();
        $installedThemes = self::getInstalledThemes();

        $themesList = PublicAPI::getData("resources/getResources&resource_type=0");
        View::createAdminView("core", "themeConfiguration")
            ->addStyle("admin/resources/vendors/simple-datatables/style.css", "admin/resources/assets/css/pages/simple-datatables.css")
            ->addVariableList(["currentTheme" => $currentTheme, "installedThemes" => $installedThemes, "themesList" => $themesList])
            ->addScriptAfter("admin/resources/vendors/simple-datatables/umd/simple-datatables.js", "admin/resources/assets/js/pages/simple-datatables.js")
            ->view();
    }

    #[Link("/configuration", Link::POST, [], "/cmw-admin/theme")]
    public function adminThemeConfigurationPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.theme.configuration");

        $theme = filter_input(INPUT_POST, "theme");

        CoreModel::updateOption("theme", $theme);

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("core.toaster.config.success"));

        header("Location: configuration");
    }

    #[Link("/configuration/regenerate", Link::POST, [], "/cmw-admin/theme")]
    public function adminThemeConfigurationRegeneratePost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.theme.configuration");

        $themeName = self::getCurrentTheme()->getName();
        $this->themeModel->deleteThemeConfig($themeName);
        $this->installThemeSettings($themeName);

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("core.toaster.theme.regenerate"));

        header("Location: ../configuration");
    }

    #[Link("/install/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/theme")]
    public function adminThemeInstallation(int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.theme.configuration");

        $theme = PublicAPI::getData("resources/installResource&id=$id");

        if (!DownloadManager::installPackageWithLink($theme['file'], "theme", $theme['name'])){
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.downloads.errors.internalError",
                    ['name' => $theme['name'], 'version' => $theme['version_name']]));
            Redirect::redirectPreviousRoute();
            return;
        }

        //Install theme settings
        $this->installThemeSettings($theme['name']);
        CoreModel::updateOption("theme", $theme['name']);

        //TODO TOASTER

        Redirect::redirectPreviousRoute();
    }


    #[Link("/manage", Link::GET, [], "/cmw-admin/theme")]
    public function adminThemeManage(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.theme.configuration");
        View::createAdminView("core", "themeManage")
            ->addStyle("admin/resources/vendors/summernote/summernote-lite.css", "admin/resources/assets/css/pages/summernote.css")
            ->addScriptAfter("admin/resources/vendors/jquery/jquery.min.js", "admin/resources/vendors/summernote/summernote-lite.min.js", "admin/resources/assets/js/pages/summernote.js")
            ->view();
    }

    #[Link("/manage", Link::POST, [], "/cmw-admin/theme")]
    public function adminThemeManagePost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.theme.configuration");

        $aresFiles = [];

        // Manage files
        foreach ($_FILES as $conf => $file) {
            $aresFiles['__images__'][$conf] = true;

            //If file is empty, we don't update the config.
            if ($file['name'] !== "") {

                $imageName = ImagesManager::upload($file, self::getCurrentTheme()->getName() . "/img");
                if (!str_contains($imageName, "ERROR")) {
                    $remoteImageValue = ThemeModel::fetchConfigValue($conf);
                    $localImageValue = (new ThemeController())->getCurrentThemeConfigSetting($conf);

                    if ($remoteImageValue !== $file && $remoteImageValue !== $localImageValue) {
                        ImagesManager::deleteImage(self::getCurrentTheme()->getName() . "/img/$remoteImageValue");
                    }

                    $this->themeModel->updateThemeConfig($conf, $imageName, self::getCurrentTheme()->getName());
                } else {
                    Response::sendAlert("error", LangManager::translate("core.toaster.error"),
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
                $this->themeModel->updateThemeConfig($conf, $_POST[$conf] ?? "0", self::getCurrentTheme()->getName());
            }
        }

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("core.toaster.config.success"));

        header("location: manage");
    }

}

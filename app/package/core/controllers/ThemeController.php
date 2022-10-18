<?php

namespace CMW\Controller\Core;

use CMW\Entity\Core\ThemeEntity;
use CMW\Model\Core\CoreModel;
use CMW\Model\Core\ThemeModel;
use CMW\Router\Link;
use CMW\Utils\Utils;
use CMW\Utils\View;
use Error;
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

    public static function getInstalledThemes(): array
    {
        $toReturn = array();
        $themesFolder = 'public/themes';
        $contentDirectory = array_diff(scandir("$themesFolder/"), array('..', '.'));
        foreach ($contentDirectory as $theme) {
            if(file_exists("$themesFolder/$theme/infos.json")) {
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

        if(!file_exists($themeConfigFile)) {
            return [];
        }

        $content = include $themeConfigFile;

        if(!is_array($content)){
            return [];
        }

        return $content;
    }

    public function installThemeSettings(String $theme): void
    {
        $themeConfigFile = "public/themes/$theme/config/config.settings.php";

        if(!file_exists($themeConfigFile)) {
            return;
        }

        $content = include $themeConfigFile;


        foreach ($content as $config => $value){
            $this->themeModel->storeThemeConfig($config, $value, $theme);
        }
    }


    /* ADMINISTRATION */

    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin/theme")]
    #[Link("/configuration", Link::GET, [], "/cmw-admin/theme")]
    public function adminThemeConfiguration(): void
    {
        $currentTheme = self::getCurrentTheme();
        $installedThemes = self::getInstalledThemes();

        try {
            $themesList = json_decode(file_get_contents(Utils::getApi() . "/getThemeList"), false, 512, JSON_THROW_ON_ERROR);
            View::createAdminView("core", "themeConfiguration")
                ->addVariableList(["currentTheme" => $currentTheme, "installedThemes" => $installedThemes, "themesList" => $themesList])
                ->view();
        } catch (JsonException $e) {
            throw new Error($e);
        }

    }

    #[Link("/configuration", Link::POST, [], "/cmw-admin/theme")]
    public function adminThemeConfigurationPost(): void
    {
        $theme = filter_input(INPUT_POST, "theme");

        CoreModel::updateOption("theme", $theme);
        header("Location: configuration");
    }

    #[Link("/install/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/theme")]
    public function adminThemeInstallation(int $id): void
    {
        try {
            $theme = json_decode(file_get_contents(Utils::getApi() . "/getThemeById=" . $id), false, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
        }


        //VARS
        $url = "https://devcmw.w3b.websr.fr/public/uploads/market/files/" . $theme->file; // TODO GET REAL LINK
        $outFileName = "public/uploads/" . $theme->file;

        //Download & store File
        set_time_limit(0);
        $file = file_get_contents($url);
        file_put_contents($outFileName, $file);

        $zip = new ZipArchive();
        if ($zip->open($outFileName)) {
            $zip->extractTo('public/themes/');
            $zip->close();
            unlink($outFileName);
        }

        //Install theme settings
        $this->installThemeSettings($theme->name);

        header("location: /cmw-admin/theme/configuration");
    }


    #[Link("/manage", Link::GET, [], "/cmw-admin/theme")]
    public function adminThemeManage(): void
    {
        View::createAdminView("core", "themeManage")
            ->view();
    }

    #[Link("/manage", Link::POST, [], "/cmw-admin/theme")]
    public function adminThemeManagePost(): void
    {
        foreach ($this->getCurrentThemeConfigSettings() as $conf => $value) {
            $this->themeModel->updateThemeConfig($conf, $_POST[$conf], self::getCurrentTheme()->getName());
        }

        header("location: /cmw-admin/theme/manage");
    }

}

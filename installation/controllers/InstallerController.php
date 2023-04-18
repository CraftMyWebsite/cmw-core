<?php

namespace CMW\Controller\Installer;

use CMW\Controller\Core\ThemeController;
use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Download\DownloadManager;
use CMW\Manager\Error\ErrorManager;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Core\CoreModel;
use CMW\Router\Link;
use CMW\Router\LinkStorage;
use CMW\Utils\Redirect;
use CMW\Utils\Response;
use CMW\Utils\Utils;
use CMW\Manager\Views\View;
use InstallerModel;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @installerController
 * @package installer
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class InstallerController
{

    static public float $minPhpVersion = 8.1;
    static public int $minPhpVersionId = 80100;
    static public array $requiredSettings = ['php', 'zip', 'curl', 'pdo'];

    static public array $installSteps = [0 => "welcome", 1 => "config", 2 => "details", 3 => "bundle", 4 => "packages",
                                        5 => "themes", 6 => "admin", 7 => "finish"];

    /**
     * @return bool
     * @desc Check if the website has all the required configurations to start the installation
     */
    public static function checkAllRequired(): bool
    {
        foreach (self::$requiredSettings as $required) {
            if (!self::hasRequired($required)){
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $value
     * @return bool
     * @desc Return true if the website has the specified required
     */
    public static function hasRequired(string $value): bool
    {
        return match ($value){
            "php" => PHP_VERSION_ID >= self::$minPhpVersionId,
            "https" => Utils::getHttpProtocol() === "https",
            "zip" => extension_loaded('zip'),
            "curl" => extension_loaded('curl'),
            "pdo" => extension_loaded('pdo')
        };
    }

    /**
     * @param string $value
     * @return string
     * @desc Return formatted style
     */
    public static function hasRequiredFormatted(string $value): string
    {
        return self::hasRequired($value) ? "<i class='text-green-500 fa-solid fa-check'></i>" :
            "<i class='text-red-500 fa-solid fa-xmark'></i>";
    }

    public static function getInstallationStep(): int
    {
        return Utils::getEnv()->getValue("installStep");
    }

    public static function loadLang(): ?array
    {

        $lang = Utils::getEnv()->getValue("locale") ?? "en";
        $fileName = Utils::getEnv()->getValue("dir") . "/installation/lang/$lang.php";

        $fileExist = is_file($fileName);

        if (!$fileExist) {
            return null;
        }

        $fileContent = include $fileName;

        if (!is_array($fileContent)) {
            return null;
        }

        return $fileContent;
    }



    #[Link(path: "/lang/:code", method: Link::GET, variables: ["code" => ".*?"], scope: "/installer")]
    public function changeLang(string $code): void
    {
        Utils::getEnv()->setOrEditValue("LOCALE", $code);
        header("location: ../../installer");
    }

    private function loadView(string $filename): void
    {
        $lang = Utils::getEnv()->getValue("locale") ?? "fr";

        $view = new View(basicVars: false);
        $view
            ->setCustomPath(Utils::getEnv()->getValue("DIR"). "installation/views/$filename.view.php")
            ->setCustomTemplate(Utils::getEnv()->getValue("DIR") . "installation/views/template.php")
            ->addStyle("admin/resources/vendors/iziToast/iziToast.min.css")
            ->addScriptAfter("admin/resources/vendors/iziToast/iziToast.min.js")
            ->addVariableList(['lang' => $lang]);

        $view->view();
    }

    #[Link(path: "/", method: Link::GET, scope: "/installer")]
    public function getInstallPage(): void
    {
        $value = match (self::getInstallationStep()) {
            1 => "firstInstall",
            2 => "secondInstall",
            3 => "thirdInstall",
            4 => "fourthInstall",
            5 => "fifthInstall",
            6 => "sixInstall",
            7 => "finishInstall",
            default => "welcomeInstall"
        };

        $this->loadView($value);
    }

    #[Link(path: "/submit", method: Link::POST, scope: "/installer", secure: false)]
    public function postInstallPage(): void {
        $value = match (self::getInstallationStep()) {
            1 => "firstInstallPost",
            2 => "secondInstallPost",
            3 => "thirdInstallPost",
            4 => "fourthInstallPost",
            5 => "fifthInstallPost",
            6 => "sixInstallPost",
            default => "welcomeInstallPost"
        };

        $this->$value();

        Redirect::redirectPreviousRoute();
    }

    public function welcomeInstallPost(): void
    {
        $remoteAddress = $_SERVER['REMOTE_ADDR'];

        if(!filter_var($remoteAddress,  FILTER_VALIDATE_IP)){
            $remoteAddress = "0.0.0.0";
        }

        $data = [
            'domain' => $_SERVER['HTTP_HOST'],
            'cmw_version' => Utils::getEnv()->getValue('VERSION'),
            'remote_address' => $remoteAddress
        ];

        $apiReturn = PublicAPI::postData("websites/register", $data, false);

        if (array_key_exists('uuid', $apiReturn)){
            Utils::getEnv()->setOrEditValue('CMW_KEY', $apiReturn['uuid']);
        } else {
            Utils::getEnv()->setOrEditValue('CMW_KEY', 'ERROR');
        }

        Utils::getEnv()->editValue("installStep", 1);
    }

    /**
     * @throws \JsonException
     */
    #[Link(path: "/test/db", method: Link::POST, scope: "/installer", secure: false)]
    public function testDbConnection(): void
    {

        [$host, $username, $password, $port] = Utils::filterInput("bdd_address", "bdd_login", "bdd_pass", "bdd_port");

        if(InstallerModel::tryDatabaseConnection($host, $username, $password, $port)) {
            print (json_encode(["status" => 1, "content" =>
                LangManager::translate("core.toaster.db.config.success")],
                JSON_THROW_ON_ERROR));
        } else {
            print (json_encode(["status" => 0,
                "content" => LangManager::translate("core.toaster.db.config.error")],
                JSON_THROW_ON_ERROR));
        }
    }

    public function firstInstallPost(): void
    {
        if (Utils::isValuesEmpty($_POST, "bdd_name", "bdd_login", "bdd_address", "bdd_port", "install_folder")) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.db.missing_inputs"));
            return;
        }

        [$host, $username, $password, $port] = Utils::filterInput("bdd_address", "bdd_login", "bdd_pass", "bdd_port");

        $db = filter_input(INPUT_POST, "bdd_name") ?? "cmw";

        $subFolder = filter_input(INPUT_POST, "install_folder");
        $devMode = isset($_POST['dev_mode']);
        $timezone = date_default_timezone_get(); //TODO GET BROWSER TIMEZONE

        if (!InstallerModel::tryDatabaseConnection($host, $username, $password, $port)) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.db.config.error"));
            return;
        }

        $this->firstInstallSetDatabase($host, $db, $username, $password, $port);
        $this->firstInstallSetInfos($subFolder, $timezone, $devMode);

        Utils::getEnv()->setOrEditValue("PATH_SUBFOLDER", $subFolder);
        Utils::getEnv()->setOrEditValue("PATH_URL", Utils::getCompleteUrl());
        Utils::getEnv()->setOrEditValue("PATH_ADMIN_VIEW", "admin/resources/views/");
        Utils::getEnv()->setOrEditValue("TIMEZONE", $timezone);
        Utils::getEnv()->setOrEditValue("DEVMODE", $devMode);
        Utils::getEnv()->setOrEditValue("UPDATE_CHECKER", "1");


        //Todo Throw error
        InstallerModel::initDatabase($host, $db, $username, $password, $port);

        // Install the default theme settings
        (new ThemeController())->installThemeSettings(ThemeController::getCurrentTheme()->getName());

        //Init default routes
        (new LinkStorage())->storeDefaultRoutes();

        Utils::getEnv()->editValue("installStep", 2);
    }

    public function secondInstallPost(): void
    {
        if (Utils::isValuesEmpty($_POST, "config_name", "config_description")) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.db.missing_inputs"));
            return;
        }

        $name = filter_input(INPUT_POST, "config_name");
        $description = filter_input(INPUT_POST, "config_description");

        InstallerModel::initConfig($name, $description);

        Utils::getEnv()->editValue("installStep", 3);
    }

    public function thirdInstallPost(): void
    {
        $isCustom = false;

        if (!isset($_POST['bundleId'])){
            $isCustom = true;
        }

        // If custom bundle is select, we skip this step
        if ($isCustom){
            Utils::getEnv()->editValue("installStep", 4);
            return;
        }

        $bundleId = $_POST['bundleId'];

        $resources = PublicAPI::getData("resources/installBundle&id=$bundleId");

        foreach ($resources as $resource){
            $type = $resource['type'] === 1 ? 'package' : 'theme';

            if (!DownloadManager::installPackageWithLink($resource['file'], $type, $resource['name'])) {
                LangManager::translate("core.downloads.errors.internalError",
                    ['name' => $resource['name'], 'version' => $resource['version_name']]);
                continue;
            }

            if ($type === 'theme'){
                (new ThemeController())->installThemeSettings($resource['name']);
                CoreModel::updateOption("theme", $resource['name']);
            }
        }

        Utils::getEnv()->editValue("installStep", 6);
    }

    public function fourthInstallPost(): void {

        if (!isset($_POST['packages'])){
            Utils::getEnv()->editValue("installStep", 5);
            return;
        }

        foreach ($_POST['packages'] as $id) {

            $package = PublicAPI::getData("resources/installResource&id=$id");

            $type = $package['type'] === 1 ? 'package' : 'theme';

            if (!DownloadManager::installPackageWithLink($package['file'], $type, $package['name'])) {
                Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                    LangManager::translate("core.downloads.errors.internalError",
                        ['name' => $package['name'], 'version' => $package['version_name']]));
            }

        }

        Utils::getEnv()->editValue("installStep", 5);
    }

    public function fifthInstallPost(): void {

        if (!isset($_POST['theme'])){
            Utils::getEnv()->editValue("installStep", 6);
            return;
        }

        $id = filter_input(INPUT_POST, "theme");

        $theme = PublicAPI::getData("resources/installResource&id=$id");


        if (!DownloadManager::installPackageWithLink($theme['file'], 'theme', $theme['name'])) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.downloads.errors.internalError",
                    ['name' => $theme['name'], 'version' => $theme['version_name']]));

            return;
        }

        (new ThemeController())->installThemeSettings($theme['name']);
        CoreModel::updateOption("theme", $theme['name']);

        Utils::getEnv()->editValue("installStep", 6);
    }

    public function sixInstallPost(): void
    {
        if (Utils::isValuesEmpty($_POST, "email", "pseudo", "password") || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.db.missing_inputs"));
            return;
        }

        $email = filter_input(INPUT_POST, "email");
        $pseudo = filter_input(INPUT_POST, "pseudo");
        $password = password_hash(filter_input(INPUT_POST, "password"), PASSWORD_BCRYPT);

        InstallerModel::initAdmin($email, $pseudo, $password);

        Utils::getEnv()->editValue("installStep", 7);
    }

    private function firstInstallSetDatabase(string $host, string $db, string $username, string $password, int $port): void
    {
        Utils::getEnv()->setOrEditValue("DB_HOST", $host);
        Utils::getEnv()->setOrEditValue("DB_NAME", $db);
        Utils::getEnv()->setOrEditValue("DB_USERNAME", $username);
        Utils::getEnv()->setOrEditValue("DB_PASSWORD", $password);
        Utils::getEnv()->setOrEditValue("DB_PORT", $port);
    }

    private function firstInstallSetInfos(string $subFolder, string $timezone, bool $devMode): void
    {
        Utils::getEnv()->setOrEditValue("PATH_SUBFOLDER", $subFolder);
        Utils::getEnv()->setOrEditValue("TIMEZONE", $timezone);
        Utils::getEnv()->setOrEditValue("DEVMODE", $devMode);
    }

    #[NoReturn] public static function goToInstall(): void
    {
        $path = $_SERVER["REQUEST_URI"];
        $path = explode("/", $path);

        if (in_array("installer", $path)) {
            return;
        }

        ob_start();
        header("Location: installer/");
        die();
    }

    #[Link(path: "/finish", method: Link::GET, scope: "/installer")]
    public function endInstallation(): void
    {
        // Reset to default settings (with dev mode or not)
        ErrorManager::enableErrorDisplays();
        Utils::getEnv()->editValue("installStep", -1);

       header("location: " . Utils::getEnv()->getValue('PATH_SUBFOLDER'));
    }


}
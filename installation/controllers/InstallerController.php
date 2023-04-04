<?php

namespace CMW\Controller\Installer;

use CMW\Controller\Core\ThemeController;
use CMW\Controller\Installer\Games\FabricGames;
use CMW\Manager\Lang\LangManager;
use CMW\Router\Link;
use CMW\Router\LinkStorage;
use CMW\Utils\Redirect;
use CMW\Utils\Response;
use CMW\Utils\Utils;
use CMW\Utils\View;
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

    static public array $installSteps = [0 => "welcome", 1 => "config", 2 => "details", 3 => "bundle", 4 => "packages",
                                        5 => "themes", 6 => "admin", 7 => "finish"];

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
            default => "welcomeInstallPost"
        };

        $this->$value();

        Redirect::redirectPreviousRoute();
    }

    public function welcomeInstallPost(): void
    {
        Utils::getEnv()->editValue("installStep", 1);
    }

    /**
     * @throws \JsonException
     */
    #[Link(path: "/test/db", method: Link::POST, scope: "/installer", secure: false)]
    public function testDbConnection(): void
    {
        $host = filter_input(INPUT_POST, "bdd_address");
        $username = filter_input(INPUT_POST, "bdd_login");
        $password = filter_input(INPUT_POST, "bdd_pass");
        $port = filter_input(INPUT_POST, "bdd_port");

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

        $host = filter_input(INPUT_POST, "bdd_address");
        $username = filter_input(INPUT_POST, "bdd_login");
        $password = filter_input(INPUT_POST, "bdd_pass");
        $db = filter_input(INPUT_POST, "bdd_name");
        $port = filter_input(INPUT_POST, "bdd_port");

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
        InstallerModel::initDatabase($host, $db, $username, $password, $port, $devMode);

        // Install the default theme settings
        (new ThemeController())->installThemeSettings(ThemeController::getCurrentTheme()->getName());

        //Init default routes
        (new LinkStorage())->storeDefaultRoutes();

        Utils::getEnv()->editValue("installStep", 2);
    }

    public function secondInstallPost(): void
    {
        require_once(Utils::getEnv()->getValue("dir") . "installation/tools/FabricGames.php");

        if (Utils::isValuesEmpty($_POST, "game")) {
            echo "-1";
            return;
        }

        $selGame = filter_input(INPUT_POST, "game");

        FabricGames::installGame($selGame);

        Utils::getEnv()->setOrEditValue("game", $selGame);

        Utils::getEnv()->editValue("installStep", 3);

        echo '1';
    }

    public function thirdInstallPost(): void
    {
        if (Utils::isValuesEmpty($_POST, "email", "username", "password") || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            echo "-1";
            return;
        }

        $email = filter_input(INPUT_POST, "email");
        $username = filter_input(INPUT_POST, "username");
        $password = password_hash(filter_input(INPUT_POST, "password"), PASSWORD_BCRYPT);

        InstallerModel::initAdmin($email, $username, $password);

        Utils::getEnv()->editValue("installStep", 4);

        echo '1';
    }

    public function fourthInstallPost(): void {
        require_once(Utils::getEnv()->getValue("dir") . "installation/tools/FabricGames.php");

        if (Utils::isValuesEmpty($_POST, "config_name", "config_description")) {
            echo "-1";
            return;
        }

        $name = filter_input(INPUT_POST, "config_name");
        $description = filter_input(INPUT_POST, "config_description");

        InstallerModel::initConfig($name, $description);

        $res = FabricGames::initConfig();

        if($res !== 1) {
            echo $res;
            return;
        }

        Utils::getEnv()->editValue("installStep", 5);

        echo 1;
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


    public function loadHTMLGame(): void
    {
        require_once(Utils::getEnv()->getValue("dir") . "installation/tools/FabricGames.php");

        FabricGames::getHTML();
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

    public function endInstallation(): void
    {
        Utils::getEnv()->editValue("installStep", -1);
    }


}
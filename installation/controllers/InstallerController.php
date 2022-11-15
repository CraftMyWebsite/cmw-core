<?php

namespace CMW\Controller\Installer;

use CMW\Controller\Core\ThemeController;
use CMW\Controller\Installer\Games\FabricGames;
use CMW\Router\Link;
use CMW\Router\LinkStorage;
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

    public function __construct()
    {
        $this->loadLang();
    }


    private function loadLang(): void
    {
        $lang = Utils::getEnv()->getValue("locale") ?? "fr";
        require_once(Utils::getEnv()->getValue("dir") . "/installation/lang/$lang.php");
    }

    #[Link(path: "/lang/:code", method: Link::GET, variables: ["code" => ".*?"], scope: "/installer")]
    public function changeLang(string $code): void
    {
        Utils::getEnv()->setOrEditValue("LOCALE", $code);
        header("location: ../../installer");
    }

    private function loadView(string $filename): void
    {
        $install = new InstallerController();

        $view = new View(basicVars: false);
        $view
            ->setCustomPath(Utils::getEnv()->getValue("DIR"). "installation/views/$filename.view.php")
            ->setCustomTemplate(Utils::getEnv()->getValue("DIR") . "installation/views/template.php")
            ->addVariable("install", $install);

        $view->view();
    }

    public function getInstallationStep(): int
    {
        return Utils::getEnv()->getValue("installStep");
    }

    public function setActiveOnStep(int $step): string
    {
        return $this->getInstallationStep() === $step ? "active" : "";
    }

    public function setCheckOnStep(int $step): string
    {
        return (($this->getInstallationStep() > $step) || $this->getInstallationStep() === -1) ? "check" : "spinner";
    }

    public function getGameList(): array
    {
        require_once(Utils::getEnv()->getValue("dir") . "installation/tools/FabricGames.php");

        return FabricGames::getGameList();
    }

    #[Link(path: "/", method: Link::GET, scope: "/installer")]
    public function getInstallPage(): void
    {
        $value = match ($this->getInstallationStep()) {
            1 => "secondInstall",
            2 => "thirdInstall",
            3 => "fourthInstall",
            4 => "fifthInstall",
            default => "firstInstall"
        };

        $this->loadView($value);
    }

    #[Link(path: "/submit", method: Link::POST, scope: "/installer", secure: false)]
    public function postInstallPage(): void {
        $value = match ($this->getInstallationStep()) {
            1 => "secondInstallPost",
            2 => "thirdInstallPost",
            3 => "fourthInstallPost",
            default => "firstInstallPost"
        };

        $this->$value();
    }

    public function firstInstallPost(): void
    {
        if (Utils::isValuesEmpty($_POST, "bdd_name", "bdd_login", "bdd_address")) {
            echo "-1";
            return;
        }

        $host = filter_input(INPUT_POST, "bdd_address");
        $username = filter_input(INPUT_POST, "bdd_login");
        $password = filter_input(INPUT_POST, "bdd_pass");
        $db = filter_input(INPUT_POST, "bdd_name");

        $subFolder = filter_input(INPUT_POST, "install_folder");
        $devMode = isset($_POST['dev_mode']);
        $timezone = date_default_timezone_get(); //TODO GET BROWSER TIMEZONE

        if (!InstallerModel::tryDatabaseConnection($host, $db, $username, $password)) {
            echo '-2';
            return;
        }

        $this->firstInstallSetDatabase($host, $db, $username, $password);
        $this->firstInstallSetInfos($subFolder, $timezone, $devMode);

        Utils::getEnv()->setOrEditValue("PATH_SUBFOLDER", $subFolder);
        Utils::getEnv()->setOrEditValue("PATH_URL", Utils::getCompleteUrl());
        Utils::getEnv()->setOrEditValue("PATH_ADMIN_VIEW", "admin/resources/views/");
        Utils::getEnv()->setOrEditValue("TIMEZONE", $timezone);
        Utils::getEnv()->setOrEditValue("DEVMODE", $devMode);


        //Todo Throw error
        InstallerModel::initDatabase($host, $db, $username, $password, $devMode);

        // Install the default theme settings
        (new ThemeController())->installThemeSettings(ThemeController::getCurrentTheme()->getName());

        //Init default routes
        (new LinkStorage())->storeDefaultRoutes();

        Utils::getEnv()->editValue("installStep", 1);

        echo '1';
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

        Utils::getEnv()->editValue("installStep", 2);

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

        Utils::getEnv()->editValue("installStep", 3);

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

        Utils::getEnv()->editValue("installStep", 4);

        echo 1;
    }

    private function firstInstallSetDatabase(string $host, string $db, string $username, string $password): void
    {
        Utils::getEnv()->setOrEditValue("DB_HOST", $host);
        Utils::getEnv()->setOrEditValue("DB_NAME", $db);
        Utils::getEnv()->setOrEditValue("DB_USERNAME", $username);
        Utils::getEnv()->setOrEditValue("DB_PASSWORD", $password);
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
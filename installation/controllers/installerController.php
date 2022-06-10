<?php

namespace CMW\Controller\Installer;

use CMW\Controller\Installer\Games\FabricGames;
use CMW\Utils\Utils;
use installerModel;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @installerController
 * @package installer
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class installerController
{

    private Utils $Utils;

    public function __construct()
    {
        global $_UTILS;
        $this->Utils = $_UTILS;
        $this->loadLang();
    }


    private function loadLang(): void
    {
        $lang = $this->Utils::getEnv()->getValue("locale") ?: "fr";
        require_once($this->Utils::getEnv()->getValue("dir") . "/installation/lang/$lang.php");
    }

    public function changeLang(string $code): void
    {
        $this->Utils::getEnv()->setOrEditValue("LOCALE", $code);
        header("location: ../../installer");
    }

    private function loadView(string $filename): void
    {
        $install = new installerController();
        ob_start();

        extract(["install" => $install], EXTR_OVERWRITE);
        require_once($this->Utils::getEnv()->getValue("dir") . "/installation/views/$filename.view.php");
        $content = ob_get_clean();
        require_once($this->Utils::getEnv()->getValue("dir") . "/installation/views/template.php");
    }

    public function getInstallationStep(): int
    {
        return $this->Utils::getEnv()->getValue("installStep");
    }

    public function setActiveOnStep(int $step): string
    {
        return $this->getInstallationStep() === $step ? "active" : "";
    }

    public function setCheckOnStep(int $step): string
    {
        return (($this->getInstallationStep() > $step) || $this->getInstallationStep() == -1) ? "check" : "spinner";
    }

    public function getGameList(): array
    {
        require_once($this->Utils::getEnv()->getValue("dir") . "installation/tools/FabricGames.php");

        return FabricGames::getGameList();
    }

    public function firstInstallView(): void
    {
        $this->loadView("firstInstall");
    }

    public function secondInstallView(): void
    {
        $this->loadView("secondInstall");
    }

    public function thirdInstallView(): void
    {
        $this->loadView("thirdInstall");
    }

    public function fourthInstallView(): void
    {
        $this->loadView("fourthInstall");
    }

    public function fifthInstallView(): void
    {
        $this->loadView("fifthInstall");
    }

    public function firstInstallPost(): void
    {
        if ($this->Utils::isValuesEmpty($_POST, "bdd_name", "bdd_login", "bdd_address")) {
            echo "-1";
            return;
        }

        $host = filter_input(INPUT_POST, "bdd_address");
        $username = filter_input(INPUT_POST, "bdd_login");
        $password = filter_input(INPUT_POST, "bdd_pass");
        $db = filter_input(INPUT_POST, "bdd_name");

        $subFolder = filter_input(INPUT_POST, "install_folder");
        $devMode = isset($_POST['dev_mode']);
        $timezone = date_default_timezone_get();

        if (!installerModel::tryDatabaseConnection($host, $db, $username, $password)) {
            echo '-2';
            return;
        }

        $this->firstInstallSetDatabase($host, $db, $username, $password);
        $this->firstInstallSetInfos($subFolder, $timezone, $devMode);

        $this->Utils::getEnv()->setOrEditValue("PATH_SUBFOLDER", $subFolder);
        $this->Utils::getEnv()->setOrEditValue("PATH_ADMIN_VIEW", "admin/resources/views/");
        $this->Utils::getEnv()->setOrEditValue("TIMEZONE", $timezone);
        $this->Utils::getEnv()->setOrEditValue("DEVMODE", $devMode);


        //Todo Throw error
        installerModel::initDatabase($host, $db, $username, $password, $devMode);

        $this->Utils::getEnv()->editValue("installStep", 1);

        echo '1';
    }

    public function secondInstallPost(): void
    {
        require_once($this->Utils::getEnv()->getValue("dir") . "installation/tools/FabricGames.php");

        if ($this->Utils::isValuesEmpty($_POST, "game")) {
            echo "-1";
            return;
        }

        $selGame = filter_input(INPUT_POST, "game");

        FabricGames::installGame($selGame);

        $this->Utils::getEnv()->setOrEditValue("game", $selGame);

        $this->Utils::getEnv()->editValue("installStep", 2);

        echo '1';
    }

    public function thirdInstallPost(): void
    {
        if ($this->Utils::isValuesEmpty($_POST, "email", "username", "password") || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            echo "-1";
            return;
        }

        $email = filter_input(INPUT_POST, "email");
        $username = filter_input(INPUT_POST, "username");
        $password = password_hash(filter_input(INPUT_POST, "password"), PASSWORD_BCRYPT);

        installerModel::initAdmin($email, $username, $password);

        $this->Utils::getEnv()->editValue("installStep", 3);

        echo '1';
    }

    public function fourthInstallPost(): void {
        require_once($this->Utils::getEnv()->getValue("dir") . "installation/tools/FabricGames.php");

        if ($this->Utils::isValuesEmpty($_POST, "config_name", "config_description")) {
            echo "-1";
            return;
        }

        $name = filter_input(INPUT_POST, "config_name");
        $description = filter_input(INPUT_POST, "config_description");

        installerModel::initConfig($name, $description);

        $res = FabricGames::initConfig();

        if($res !== 1) {
            echo $res;
            return;
        }

        $this->Utils::getEnv()->editValue("installStep", 4);

        echo 1;
    }

    private function firstInstallSetDatabase(string $host, string $db, string $username, string $password): void
    {
        $this->Utils::getEnv()->setOrEditValue("DB_HOST", $host);
        $this->Utils::getEnv()->setOrEditValue("DB_NAME", $db);
        $this->Utils::getEnv()->setOrEditValue("DB_USERNAME", $username);
        $this->Utils::getEnv()->setOrEditValue("DB_PASSWORD", $password);
    }

    private function firstInstallSetInfos(string $subFolder, string $timezone, bool $devMode): void
    {
        $this->Utils::getEnv()->setOrEditValue("PATH_SUBFOLDER", $subFolder);
        $this->Utils::getEnv()->setOrEditValue("TIMEZONE", $timezone);
        $this->Utils::getEnv()->setOrEditValue("DEVMODE", $devMode);
    }


    public function loadHTMLGame(): void
    {
        require_once($this->Utils::getEnv()->getValue("dir") . "installation/tools/FabricGames.php");

        FabricGames::getHTML();
    }


    #[NoReturn] public function goToInstall(): void
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
        $this->Utils::getEnv()->editValue("installStep", -1);
    }


}
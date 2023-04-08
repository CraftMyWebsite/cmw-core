<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Updater\UpdatesManager;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Model\Core\CoreModel;
use CMW\Router\Link;
use CMW\Router\RouterException;
use CMW\Utils\Response;
use CMW\Utils\Utils;
use CMW\Manager\Views\View;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @coreController
 * @package Core
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class CoreController
{
    public static string $themeName;
    public static array $availableLocales = ['fr' => 'Français', 'en' => 'English'];
    public static array $exampleDateFormat = ["d-m-Y H:i:s", "d-m-Y Hh im ss", "d/m/Y H:i:s", "d/m/Y à H\h i\m s\s", "d/m/Y à H\h i\m", "d/m/Y at H\h i\m s\s"];

    public function __construct()
    {
        self::$themeName = (new CoreModel())->fetchOption("theme"); //Get the current active theme
    }

    public static function getThemePath(): string {
        return (empty($themeName = self::$themeName)) ? "" : "./public/themes/$themeName/";
    }

    public static function formatDate(string $date): string
    {
        return date((new CoreModel())->fetchOption("dateFormat"), strtotime($date));
    }

    /* ADMINISTRATION */
    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin")]
    #[Link("/dashboard", Link::GET, [], "/cmw-admin")]
    public function adminDashboard(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard");
        //Redirect to the dashboard
        if ($_GET['url'] === "cmw-admin") {
            header('Location: ' . getenv('PATH_SUBFOLDER') . 'cmw-admin/dashboard');
        }

        View::createAdminView("core", "dashboard")
        ->addScriptAfter("admin/resources/vendors/chart/chart.min.js",
                                "app/package/core/views/resources/js/dashboard.js")
        ->view();
    }

    #[Link(path: "/configuration", method: Link::GET, scope: "/cmw-admin")]
    public function adminConfiguration(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.configuration");

        View::createAdminView("core", "configuration")
        ->view();
    }

    #[Link(path: "/configuration", method: Link::POST, scope: "/cmw-admin")]
    public function adminConfigurationPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.configuration");

        foreach ($_POST as $option_name => $option_value):
            if ($option_name === "locale") {
                Utils::getEnv()->editValue("LOCALE", $option_value);
            }

            CoreModel::updateOption($option_name, $option_value);
        endforeach;

        //update favicon

        echo ImagesManager::upload($_FILES['favicon'], "favicon", false, "favicon");

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("core.toaster.config.success"));

        header("location: configuration");
    }

    /* PUBLIC FRONT */
    /**
     * @throws \CMW\Router\RouterException
     */
    #[Link('/', Link::GET)]
    public function frontHome(): void
    {
        $view = new View("core", "home");
        $view->view();
    }

    /**
     * @throws \CMW\Router\RouterException
     */
    #[Link("/:errorCode", Link::GET, ["errorCode" => ".*?"], "geterror")]
    public function errorView(int $errorCode = 403): void
    {
        $theme = ThemeController::getCurrentTheme()->getName();

        $errorToCall = (string)$errorCode;
        $errorFolder = "public/themes/$theme/views/errors";
        $errorFile = "$errorFolder/$errorCode.view.php";

        if (!is_file($errorFile) && is_file("$errorFolder/default.view.php")) {
            $errorToCall = "default";
        }

        $view = new View();
        $view
            ->setPackage("errors")
            ->setViewFile($errorToCall)
            ->view();
    }

    /**
     * @throws \CMW\Router\RouterException
     */
    #[Link("/:errorCode", Link::GET, ["errorCode" => ".*?"], "error")]
    public function threwRouterError($errorCode): void
    {
        throw new RouterException('Trowed Error', $errorCode);
    }

    /* //////////////////////////////////////////////////////////////////////////// */
    /* CMS FUNCTION */

    /* Security Warning */
    public function cmwWarn(): ?string
    {
        if (is_dir("installation") && getenv("DEVMODE") != 1) {
            //Todo Set that in lang file
            return <<<HTML
            <p class='security-warning'>ATTENTION - Votre dossier d'installation n'a pas encore été supprimé. Pour des questions de sécurité, vous devez supprimer le dossier installation situé à la racine de votre site.</p>
            HTML;
        }
        return null;
    }

    /*
     * Head constructor
     */
    public function cmwHead($title, $description): string
    {
        $toReturn = <<<HTML
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
            <title>$title</title>
            HTML;

        $toReturn .= PHP_EOL . '<meta name="description" content= "' . htmlspecialchars_decode($description, ENT_QUOTES) . '">
            <meta name="author" content="CraftMyWebsite">';

        return $toReturn;
    }

    /*
     * Footer constructor
     */
    public function cmwFooter(): string
    {
        $version = UpdatesManager::getVersion();
        //Todo Set that in lang
        return <<<HTML
            <p>Un site fièrement propulsé par <a href='https://craftmywebsite.fr/'>CraftMyWebsite $version</a></p>
        HTML;
    }
}


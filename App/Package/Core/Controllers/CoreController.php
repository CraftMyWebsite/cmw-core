<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Requests\Request;
use CMW\Manager\Requests\Validator;
use CMW\Manager\Updater\UpdatesManager;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Model\Core\CoreModel;
use CMW\Router\Link;
use CMW\Router\RouterException;
use CMW\Utils\Response;
use CMW\Utils\Utils;
use CMW\Manager\Views\View;

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
        self::$themeName = (new CoreModel())->fetchOption("Theme"); //Get the current active Theme
    }

    public static function getThemePath(): string {
        return (empty($themeName = self::$themeName)) ? "" : "./Public/Themes/$themeName/";
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
        ->addScriptAfter("Admin/Resources/Vendors/chart/chart.min.js",
                                "App/Package/Core/Views/Resources/Js/dashboard.js")
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
    public function adminConfigurationPost(Request $request): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.configuration");

        // TODO Test
        $validator = new Validator($request->getData());
        $validator->checkType('string', 'name')
            ->checkType('integer', 'age')
            ->checkType('boolean', 'isFdp')
            ->required('name', 'age')
            ->length('age', '1', '3');


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
        $view = new View("Core", "home");
        $view->view();
    }

    /**
     * @throws \CMW\Router\RouterException
     */
    #[Link("/:errorCode", Link::GET, ["errorCode" => ".*?"], "geterror")]
    public function errorView(Request $request, int $errorCode = 403): void
    {
        $theme = ThemeController::getCurrentTheme()->getName();

        $errorToCall = (string)$errorCode;
        $errorFolder = "Public/Themes/$theme/Views/Errors";
        $errorFile = "$errorFolder/$errorCode.view.php";

        if (!is_file($errorFile) && is_file("$errorFolder/Default.view.php")) {
            $errorToCall = "Default";
        }

        $view = new View();
        $view
            ->setPackage("Errors")
            ->setViewFile($errorToCall)
            ->view();
    }

    /**
     * @throws \CMW\Router\RouterException
     */
    #[Link("/:errorCode", Link::GET, ["errorCode" => ".*?"], "error")]
    public function threwRouterError(Request $request, $errorCode): void
    {
        throw new RouterException('Trowed Error', $errorCode);
    }

    /* //////////////////////////////////////////////////////////////////////////// */
    /* CMS FUNCTION */

    /* Security Warning */
    public function cmwWarn(): ?string
    {
        if (is_dir("Installation") && getenv("DEVMODE") != 1) {
            //Todo Set that in Lang file
            return <<<HTML
            <p class='security-warning'>ATTENTION - Votre dossier d'Installation n'a pas encore été supprimé. Pour des questions de sécurité, vous devez supprimer le dossier Installation situé à la racine de votre site.</p>
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
        //Todo Set that in Lang
        return <<<HTML
            <p>Un site fièrement propulsé par <a href='https://craftmywebsite.fr/'>CraftMyWebsite $version</a></p>
        HTML;
    }
}


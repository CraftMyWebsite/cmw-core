<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Metrics\VisitsMetricsManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Requests\Validator;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Router\Link;
use CMW\Manager\Router\RouterException;
use CMW\Manager\Updater\UpdatesManager;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Manager\Views\View;
use CMW\Model\Core\CoreModel;
use CMW\Model\Users\UsersMetricsModel;
use CMW\Utils\Redirect;

/**
 * Class: @coreController
 * @package Core
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class CoreController extends AbstractController
{
    public static string $themeName;
    public static array $availableLocales = ['fr' => 'Français', 'en' => 'English']; //todo remove that
    public static array $exampleDateFormat = ["d-m-Y H:i:s", "d-m-Y Hh im ss", "d/m/Y H:i:s", "d/m/Y à H\h i\m s\s", "d/m/Y à H\h i\m", "d/m/Y at H\h i\m s\s"];

    public static function getThemePath(): string {
        self::$themeName = CoreModel::getInstance()->fetchOption("Theme");
        return (empty($themeName = self::$themeName)) ? "" : "./Public/Themes/$themeName/";
    }

    public static function formatDate(string $date): string
    {
        return date(CoreModel::getInstance()->fetchOption("dateFormat"), strtotime($date));
    }

    /* ADMINISTRATION */
    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin")]
    #[Link("/dashboard", Link::GET, [], "/cmw-admin")]
    private function adminDashboard(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard");

        //Redirect to the dashboard
        if ($_GET['url'] === "cmw-admin") {
            header('Location: ' . getenv('PATH_SUBFOLDER') . 'cmw-admin/dashboard'); //todo redirect
        }

        $visits = (new VisitsMetricsManager())->getPastMonthsVisits(5);
        $registers = UsersMetricsModel::getInstance()->getPastMonthsRegisterNumbers(5);

        View::createAdminView("Core", "dashboard")
        ->addVariableList(['visits' => $visits, 'registers' => $registers])
        ->addScriptBefore("Admin/Resources/Vendors/Chart/chart.min.js")
        ->addScriptAfter("App/Package/Core/Views/Resources/Js/dashboard.js")
        ->view();
    }

    #[Link(path: "/configuration", method: Link::GET, scope: "/cmw-admin")]
    private  function adminConfiguration(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.configuration");

        View::createAdminView("Core", "configuration")
        ->view();
    }

    #[Link(path: "/configuration", method: Link::POST, scope: "/cmw-admin")]
    private function adminConfigurationPost(Request $request): void
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
                EnvManager::getInstance()->editValue("LOCALE", $option_value);
            }

            CoreModel::updateOption($option_name, $option_value);
        endforeach;

        //update favicon

        echo ImagesManager::upload($_FILES['favicon'], "favicon", false, "favicon"); //todo remove echo ?

        Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate("core.toaster.config.success"));

        header("location: configuration"); //todo redirect
    }

    /* PUBLIC FRONT */
    /**
     * @throws \CMW\Manager\Router\RouterException
     */
    #[Link('home', Link::GET)]
    private function redirectToHome(): void {
        Redirect::redirectToHome();
    }

    #[Link('/', Link::GET)]
    private function frontHome(): void
    {
        $view = new View("Core", "home");
        $view->view();
    }

    /**
     * @throws \CMW\Manager\Router\RouterException
     */
    #[Link("/:errorCode", Link::GET, ["errorCode" => ".*?"], "geterror")]
    private function errorView(Request $request, int $errorCode = 403): void
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
     * @throws \CMW\Manager\Router\RouterException
     */
    #[Link("/:errorCode", Link::GET, ["errorCode" => ".*?"], "error")]
    private function threwRouterError(Request $request, $errorCode): void
    {
        throw new RouterException('Trowed Error', $errorCode);
    }

    /* //////////////////////////////////////////////////////////////////////////// */
    /* CMS FUNCTION */

    /* Security Warning */
    public function cmwWarn(): ?string
    {
        if (is_dir("Installation") && EnvManager::getInstance()->getValue("DEVMODE") !== '1') {
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
    public function cmwHead(string $title, string $description): string
    {
        $desc = htmlspecialchars_decode($description, ENT_QUOTES);

        return <<<HTML
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
            <title>$title</title>
            <meta name="description" content="$desc">
            <meta name="author" content="CraftMyWebsite"> <!-- Todo review author list -->
            HTML;
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


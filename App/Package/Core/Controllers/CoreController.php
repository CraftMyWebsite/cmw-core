<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Interface\Core\IDashboardElements;
use CMW\Interface\Core\ITopBarElements;
use CMW\Manager\Cache\SimpleCacheManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Loader\Loader;
use CMW\Manager\Metrics\VisitsMetricsManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Router\RouterException;
use CMW\Manager\Theme\ThemeManager;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Manager\Views\View;
use CMW\Model\Core\CoreModel;
use CMW\Model\Users\UsersMetricsModel;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;
use JsonException;

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
    public static array $exampleDateFormat = ["d-m-Y H:i:s", "d-m-Y Hh im ss", "d/m/Y H:i:s", "d/m/Y à H\h i\m s\s", "d/m/Y à H\h i\m", "d/m/Y at H\h i\m s\s", "d F \à H\hi", "d F \at H\hi"];

    public static function getThemePath(): string
    {
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
            Redirect::redirect(EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . 'cmw-admin/dashboard');

        }

        $monthlyVisits = VisitsMetricsManager::getInstance()->getPastMonthsVisits(12);
        $dailyVisits = VisitsMetricsManager::getInstance()->getPastDaysVisits(17);
        $weeklyVisits = VisitsMetricsManager::getInstance()->getPastWeeksVisits(17);
        $registers = UsersMetricsModel::getInstance()->getPastMonthsRegisterNumbers(12);

        View::createAdminView("Core", "Dashboard/dashboard")
            ->addVariableList(['monthlyVisits' => $monthlyVisits, 'dailyVisits' => $dailyVisits,
                'weeklyVisits' => $weeklyVisits, 'registers' => $registers])
            ->addScriptBefore("Admin/Resources/Vendors/Apexcharts/Js/apexcharts.js")
            ->view();
    }

    /**
     * @return void
     * @desc Load all packages implementations for {@CMW\Interface\Core\IDashboardElements}
     */
    public function getPackagesDashboardElements(): void
    {
        $data = Loader::loadImplementations(IDashboardElements::class);

        foreach ($data as $package) {
            $package->widgets();
        }
    }

    /**
     * @return void
     * @desc Load all packages implementations for {@CMW\Interface\Core\ITopBarElements}
     */
    public function getPackagesTopBarElements(): void
    {
        $data = Loader::loadImplementations(ITopBarElements::class);

        foreach ($data as $package) {
            $package->widgets();
        }
    }

    #[Link(path: "/configuration", method: Link::GET, scope: "/cmw-admin")]
    private function adminConfiguration(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.settings.website");

        View::createAdminView("Core", "Configuration/configuration")
            ->view();
    }

    #[NoReturn] #[Link(path: "/configuration", method: Link::POST, scope: "/cmw-admin")]
    private function adminConfigurationPost(Request $request): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.settings.website");

        foreach ($_POST as $option_name => $option_value):
            if ($option_name === "locale") {
                EnvManager::getInstance()->editValue("LOCALE", $option_value);
                //TODO rename perms desc DB
            }
            CoreModel::getInstance()->updateOption($option_name, $option_value);
        endforeach;

        //update favicon
        if ($_FILES['favicon']['name'] !== "") {
            try {
                $imgStatus = ImagesManager::upload($_FILES['favicon'], "Favicon", false, "favicon");
                //Show error
                if ($imgStatus !== 'favicon.ico') {
                    Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                        LangManager::translate($imgStatus));
                } else {
                    Flash::send(Alert::SUCCESS, "Icon", LangManager::translate("core.config.alert_img"));
                }
            } catch (JsonException) {
                Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                    LangManager::translate("core.errors.editConfiguration", ['config' => 'Favicon']));
            }
        }

        $options = CoreModel::getInstance()->fetchOptions();
        SimpleCacheManager::storeCache($options, 'options', "Options");

        Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate("core.toaster.config.success"));

        Redirect::redirectPreviousRoute();
    }

    /* PUBLIC FRONT */
    /**
     * @throws \CMW\Manager\Router\RouterException
     */
    #[Link('home', Link::GET)]
    private function redirectToHome(): void
    {
        Redirect::redirectToHome();
    }

    #[Link('/', Link::GET)]
    private function frontHome(): void
    {
        $view = new View("Core", "home");
        $view->view();
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
}


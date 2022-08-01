<?php

namespace CMW\Controller\Core;

use CMW\Controller\Menus\MenusController;
use CMW\Controller\Users\UsersController;
use CMW\Model\Core\CoreModel;
use CMW\Router\Link;
use CMW\Router\RouterException;
use CMW\Utils\Response;
use CMW\Utils\Utils;
use CMW\Utils\View;
use JetBrains\PhpStorm\NoReturn;
use JsonException;

/**
 * Class: @coreController
 * @package Core
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class CoreController
{
    public static string $themePath;
    public static array $availableLocales = ['fr' => 'Français', 'en' => 'English'];

    public function __construct()
    {
        self::$themePath = $this->cmwThemePath();
    }

    #[NoReturn] protected static function redirectToHome(): void
    {
        header('Location: ' . getenv('PATH_SUBFOLDER'));
        exit();
    }

    /* ADMINISTRATION */
    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin")]
    #[Link("/dashboard", Link::GET, [], "/cmw-admin")]
    public function adminDashboard(): void
    {
        //Redirect to the dashboard
        if ($_GET['url'] === "cmw-admin") {
            header('Location: ' . getenv('PATH_SUBFOLDER') . 'cmw-admin/dashboard');
        }

        View::createAdminView("core", "dashboard")->view();
    }

    #[Link(path: "/configuration", method: Link::GET, scope: "/cmw-admin")]
    public function adminConfiguration(): void
    {
        View::createAdminView("core", "configuration")->view();
    }

    #[Link(path: "/configuration", method: Link::POST, scope: "/cmw-admin")]
    public function adminConfigurationPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.configuration");

        foreach ($_POST as $option_name => $option_value):
            if ($option_name === "locale")
                Utils::getEnv()->editValue("LOCALE", $option_value);

            CoreModel::updateOption($option_name, $option_value);
        endforeach;


        //Todo review that
        //Options with nullables options (checkbox ...)
        if (empty($_POST['minecraft_register_premium']) && getenv("GAME") === "minecraft") {
            CoreModel::updateOption("minecraft_register_premium", "false");
        }

        //TODO Remove that
        $_SESSION['toaster'][0]['title'] = CORE_TOASTER_TITLE;
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = CORE_TOASTER_CONFIG_EDIT_SUCCESS;

        header("location: configuration");
    }

    /* PUBLIC FRONT */
    #[Link('/', Link::GET)]
    public function frontHome(): void
    {
        $menu = new MenusController();

        $view = new View("core", "home");
        Response::sendAlert("success", "Ceci est un test", "Autre test");
        Response::sendAlert("success", "Second alerte !", "Dingue");
        $view->addVariable("menu", $menu)->view();
    }

    #[Link("/:errorCode", Link::GET, ["errorCode" => ".*?"], "geterror")]
    public function errorView(int $errorCode = 403): void
    {
        $theme = (new CoreController())->cmwThemePath();
        $menu = new MenusController();


        $errorToCall = (string)$errorCode;
        $errorFolder = "public/themes/$theme/views/errors";
        $errorFile = "$errorFolder/$errorCode.view.php";

        if (!is_file($errorFile) && is_file("$errorFolder/default.view.php")) {
            $errorToCall = "default";
        }

        $view = new View();
        $view
            ->setPackage("errors")
            ->addVariable("menu", $menu)
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

    public function cmwThemePath(): string
    {
        return (new CoreModel())->fetchOption("theme");
    }

    /**
     * @throws JsonException
     */
    public function cmwThemeAvailablePackages(): array
    {
        $jsonFile = file_get_contents($this->cmwThemePath() . "/infos.json");
        return json_decode($jsonFile, true, 512, JSON_THROW_ON_ERROR)["packages"];
    }

    /**
     * @throws JsonException
     */
    public function cmwPackageAvailableTheme(string $package): bool
    {
        return in_array($package, $this->cmwThemeAvailablePackages(), true);
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
        //Todo Set that in lang
        return <<<HTML
            <p>Un site fièrement propulsé par <a href='https://craftmywebsite.fr/'>CraftMyWebsite</a></p>
        HTML;
    }
}


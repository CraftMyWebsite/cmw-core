<?php

namespace CMW\Controller;

use CMW\Controller\Menus\MenusController;
use CMW\Controller\Users\UsersController;

use CMW\Model\CoreModel;
use CMW\Model\Users\UsersModel;
use CMW\Entity\Core\OptionsEntity;
use CMW\Utils\Utils;
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
    private CoreModel $coreModel;

    public function __construct($theme_path = null)
    {
        self::$themePath = $this->cmwThemePath();
        $this->coreModel = new CoreModel();
    }

    #[NoReturn] protected static function redirectToHome(): void
    {
        header('Location: ' . getenv('PATH_SUBFOLDER'));
        exit();
    }

    /* ADMINISTRATION */
    public function adminDashboard(): void
    {
        //Redirect to the dashboard
        if($_GET['url'] === "cmw-admin")
            header('Location: ' . getenv('PATH_SUBFOLDER') . 'cmw-admin/dashboard');

        view('core', 'dashboard.admin', [], 'admin', []);
    }

    public function adminConfiguration(): void
    {
        view('core', 'configuration.admin', [], 'admin', []);
    }

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
    public function frontHome(): void
    {
        $core = new CoreController();
        $menu = new MenusController();

        view('core', 'home', ["core" => $core, "menu" => $menu], 'public', []);
    }

    public function cmwThemePath(): string
    {
        $coreModel = new CoreModel();
        return $coreModel->fetchOption("theme");
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
        if (is_dir("installation") && getenv("DEVMODE") != 1 ) {
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
        return <<<HTML
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
            <title>$title</title>
            <meta name='description' content='$description'>
            <meta name='author' content='CraftMyWebsite'>
        HTML;
    }

    /*
     * Footer constructor
     */
    public function cmwFooter(): string
    {
        //Todo Set that in lang
        return <<<HTML
            <p>Un site fièrement propulsé par <a href='https://craftmywebsite.com/'>CraftMyWebsite</a></p>
        HTML;
    }
}


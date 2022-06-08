<?php

namespace CMW\Controller;

use CMW\Controller\Menus\menusController;
use CMW\Controller\Users\usersController;

use CMW\Model\coreModel;
use JsonException;

/**
 * Class: @coreController
 * @package Core
 * @author LoGuardiaN | <loguardian@hotmail.com>
 * @version 1.0
 */
class coreController
{
    public static string $themePath;

    public function __construct($theme_path = null)
    {
        self::$themePath = $this->cmwThemePath();
    }

    /* ADMINISTRATION */
    public function adminDashboard()
    {
        view('core', 'dashboard.admin', [], 'admin');
    }

    public function adminConfiguration()
    {
        view('core', 'configuration.admin', [], 'admin');
    }

    public function adminConfigurationPost()
    {
        usersController::isUserHasPermission("core.configuration");

        foreach ($_POST as $option_name => $option_value):
            coreModel::updateOption($option_name, $option_value);
        endforeach;

        //Options with nullables options (checkbox ...)
        if(empty($_POST['minecraft_register_premium']) && getenv("GAME") === "Minecraft"){
            coreModel::updateOption("minecraft_register_premium", "false");
        }

        $_SESSION['toaster'][0]['title'] = CORE_TOASTER_TITLE;
        $_SESSION['toaster'][0]['type'] = "bg-success";
        $_SESSION['toaster'][0]['body'] = CORE_TOASTER_CONFIG_EDIT_SUCCESS;

        header("location: configuration");
    }

    public function adminLanguages()
    {


        view('core', 'languages.admin', [], 'admin');
    }

    /* PUBLIC FRONT */
    public function frontHome()
    {
        $core = new coreController();
        $menu = new menusController();

        view('core', 'home', ["core" => $core, "menu" => $menu], 'public');
    }

    public function cmwThemePath(): string
    {
        $coreModel = new coreModel();
        $coreModel->fetchOption("theme");

        return $coreModel->theme;
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
        if( is_dir( "installation" ) ) {
            return "<p class='security-warning'>ATTENTION - Votre dossier d'installation n'a pas encore été supprimé. Pour des questions de sécurité, vous devez supprimer le dossier installation situé à la racine de votre site.</p>";
        }
        else {
            return null;
        }
    }

    /*
     * Head constructor
     */
    public function cmwHead($title, $description): string
    {
        $head = "<meta charset='utf-8'>";
        $head .= "<meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>";
        $head .= "<title>$title</title>";
        $head .= "<meta name='description' content='$description'>";
        $head .= "<meta name='author' content='CraftMyWebsite, Teyir, Vladort'>";
        return $head;
    }

    /*
     * Footer constructor
     */
    public function cmwFooter(): string
    {
        return "<p>Un site fierement propuslé par <a href='https://craftmywebsite.com/'>CrafyMyWebsite</a></p>";
    }
}


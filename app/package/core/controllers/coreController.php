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

    /* FRONT */
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
        return "<p>Un site fierement propuslé par <a href='https://craftmewebsite.com/'>CrafyMyWebsite</a></p>";
    }
}


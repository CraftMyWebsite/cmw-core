<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Core\CoreModel;

/**
 * Class: @SecurityController
 * @package Core
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class SecurityController extends AbstractController
{

    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin")]
    #[Link("/security", Link::GET, [], "/cmw-admin")]
    private function adminSecurity(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.security.configuration");

        View::createAdminView("Core", "security")
            ->addScriptAfter("App/Package/Core/Views/Resources/Js/security.js")
            ->addVariableList(["captcha" => self::getCaptchaType()])
            ->view();
    }

    #[Link("/security/edit/captcha", Link::POST, [], "/cmw-admin")]
    private function adminSecurityEditCaptchaPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.security.configuration");

        $captcha = filter_input(INPUT_POST, "captcha");

        switch ($captcha){
            case "captcha-hcaptcha":
                CoreModel::updateOption("captcha", "hcaptcha");
                EnvManager::getInstance()->setOrEditValue("HCAPTCHA_SITE_KEY", filter_input(INPUT_POST, "captcha_hcaptcha_site_key"));
                EnvManager::getInstance()->setOrEditValue("HCAPTCHA_SECRET_KEY", filter_input(INPUT_POST, "captcha_hcaptcha_secret_key"));
                break;
            case "captcha-recaptcha":
                CoreModel::updateOption("captcha", "recaptcha");
                EnvManager::getInstance()->setOrEditValue("RECAPTCHA_SITE_KEY", filter_input(INPUT_POST, "captcha_recaptcha_site_key"));
                EnvManager::getInstance()->setOrEditValue("RECAPTCHA_SECRET_KEY", filter_input(INPUT_POST, "captcha_recaptcha_secret_key"));
                break;
            default:
                CoreModel::updateOption("captcha", "none");
                break;
        }

        Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate("core.toaster.config.success"));

        header("Location: ../../security");
    }


    /**
     * TODO [CAPTCHA]:
     *  - Create Captcha Implementation with those methods:
     *      - show
     *      - check
     */

    /**
     * @return string
     * @Desc Get captcha name (none / hcaptcha / recaptcha)
     */
    public static function getCaptchaType(): string
    {
        return CoreModel::getOptionValue("captcha");
    }

    /**
     * @return void
     * @Desc Get the captcha config value. Theme Side.
     */
    public static function getPublicData(): void
    {
        switch (self::getCaptchaType()){
            case "hcaptcha":
                self::getPublicHCaptchaData();
                break;
            case "recaptcha":
                self::getPublicReCaptchaData();
                break;
            default:
                break;
        }

    }

    private static function getPublicHCaptchaData(): void
    {
        echo "<script src='https://js.hcaptcha.com/1/api.js' async defer></script>";
        echo '<div class="h-captcha" data-sitekey="' . EnvManager::getInstance()->getValue("HCAPTCHA_SITE_KEY") .'" 
                    data-Theme="light" data-error-callback="onError"></div>';
    }

    private static function getPublicReCaptchaData(): void
    {
        echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
        echo '<div class="g-recaptcha" data-sitekey="'. EnvManager::getInstance()->getValue("RECAPTCHA_SITE_KEY") .'"></div>';

    }


    public static function checkCaptcha(): bool
    {
        return match (self::getCaptchaType()) {
            "hcaptcha" => self::validateHCaptha(),
            "recaptcha" => self::validateReCaptha(),
            default => true,
        };
    }

    private static function validateHCaptha(): bool
    {
        $data = array(
            'secret' => EnvManager::getInstance()->getValue("HCAPTCHA_SECRET_KEY"),
            'response' => $_POST['h-captcha-response']
        );
        $verify = curl_init();
        curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
        curl_setopt($verify, CURLOPT_POST, true);
        curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($verify);

        return json_decode($response, false, 512, JSON_THROW_ON_ERROR)->success;
    }

    private static function validateReCaptha(): bool
    {
        $recaptcha = $_POST['g-recaptcha-response'];

        $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' .
            EnvManager::getInstance()->getValue("RECAPTCHA_SECRET_KEY") . '&response=' . $recaptcha;

        $response = file_get_contents($url);

       return json_decode($response, false, 512, JSON_THROW_ON_ERROR)->success;
    }




}

<?php

namespace CMW\Implementation\Core\Core;

use CMW\Interface\Core\ICaptcha;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Filter\FilterManager;
use function file_get_contents;
use function json_decode;
use const JSON_THROW_ON_ERROR;

class CaptchaRecaptchaV2Implementation implements ICaptcha
{
    public function getName(): string
    {
        return "reCAPTCHA v2";
    }

    public function getCode(): string
    {
        return "recaptcha-v2";
    }

    public function show(): void
    {
        echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
        echo '<div class="g-recaptcha" data-sitekey="' . EnvManager::getInstance()->getValue("RECAPTCHA_V2_SITE_KEY") . '"></div>';
    }

    public function validate(): bool
    {
        $recaptcha = $_POST['g-recaptcha-response'];

        $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' .
            EnvManager::getInstance()->getValue("RECAPTCHA_V2_SECRET_KEY") . '&response=' . $recaptcha;

        $response = file_get_contents($url);

        return json_decode($response, false, 512, JSON_THROW_ON_ERROR)->success;
    }

    public function adminForm(): void
    {

    }

    public function adminFormPost(): void
    {
        EnvManager::getInstance()->setOrEditValue(
            "RECAPTCHA_V2_SITE_KEY",
            FilterManager::filterInputStringPost('captcha_recaptcha_v2_site_key'),
        );
        EnvManager::getInstance()->setOrEditValue(
            "RECAPTCHA_V2_SECRET_KEY",
            FilterManager::filterInputStringPost('captcha_recaptcha_v2_secret_key'),
        );
    }
}

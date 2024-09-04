<?php

namespace CMW\Implementation\Core\Core;

use CMW\Interface\Core\ICaptcha;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Lang\LangManager;
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
        return "recaptchav2";
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
        $siteKey = EnvManager::getInstance()->getValue("RECAPTCHA_V2_SITE_KEY");
        $secretKey = EnvManager::getInstance()->getValue("RECAPTCHA_V2_SECRET_KEY");

        $infoCaptcha = LangManager::translate('core.security.free_key');

        echo <<<HTML

<script>
    const recaptchav2 = () => {
        let parent = document.getElementById("security-content-wrapper");

        let divWrapper = document.createElement("div");
        divWrapper.setAttribute("class", "grid-2");

        let divPrepend = document.createElement("div");
        divPrepend.setAttribute("class", "col-md-6");

        let divPrepend2 = document.createElement("div");
        divPrepend2.setAttribute("class", "col-md-6");

        let divInfoCaptcha = document.createElement("p");
        divInfoCaptcha.innerHTML += '{$infoCaptcha} <a class="link" href="https://www.google.com/recaptcha/" target="_blank">https://www.google.com/recaptcha/</a>';

        let divFormGroupSiteKey = document.createElement("div");
        divFormGroupSiteKey.setAttribute("class", "form-group");

        let divFormGroupSecretKey = document.createElement("div");
        divFormGroupSecretKey.setAttribute("class", "form-group");

        let labelSiteKey = document.createElement("label");
        labelSiteKey.innerHTML += "<label>Site Key :</label>";

        let labelSecreteKey = document.createElement("label");
        labelSecreteKey.innerHTML += "<label>Secret Key :</label>";

        let inputSiteKey = document.createElement("input");
        inputSiteKey.setAttribute("value", '{$siteKey}');
        inputSiteKey.setAttribute("placeholder", "Site-Key")
        inputSiteKey.setAttribute("type", "text")
        inputSiteKey.setAttribute("name", "captcha_recaptcha_v2_site_key");
        inputSiteKey.setAttribute("class", "input");
        inputSiteKey.setAttribute("required", "true");

        let inputSecretKey = document.createElement("input");
        inputSecretKey.setAttribute("value", '{$secretKey}');
        inputSecretKey.setAttribute("placeholder", "Secret-Key")
        inputSecretKey.setAttribute("type", "text")
        inputSecretKey.setAttribute("name", "captcha_recaptcha_v2_secret_key");
        inputSecretKey.setAttribute("class", "input");
        inputSecretKey.setAttribute("required", "true");


        parent.append(divWrapper);

        divWrapper.append(divPrepend);
        divPrepend.append(divFormGroupSiteKey);
        divFormGroupSiteKey.append(labelSiteKey);
        divFormGroupSiteKey.append(inputSiteKey);

        divWrapper.append(divPrepend2);
        divPrepend2.append(divFormGroupSecretKey);
        divFormGroupSecretKey.append(labelSecreteKey);
        divFormGroupSecretKey.append(inputSecretKey);

        parent.append(divInfoCaptcha);
    }
</script>
HTML;

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

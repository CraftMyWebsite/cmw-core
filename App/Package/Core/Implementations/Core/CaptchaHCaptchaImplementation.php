<?php

namespace CMW\Implementation\Core\Core;

use CMW\Interface\Core\ICaptcha;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Lang\LangManager;
use function curl_exec;
use function curl_init;
use function curl_setopt;
use function http_build_query;
use function json_decode;
use const CURLOPT_POST;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_URL;
use const JSON_THROW_ON_ERROR;

class CaptchaHCaptchaImplementation implements ICaptcha
{
    public function getName(): string
    {
        return "hCaptcha";
    }

    public function getCode(): string
    {
        return "hcaptcha";
    }

    public function show(): void
    {
        echo "<script src='https://js.hcaptcha.com/1/api.js' async defer></script>";
        echo '<div class="h-captcha" data-sitekey="' . EnvManager::getInstance()->getValue("HCAPTCHA_SITE_KEY") . '" 
                    data-Theme="light" data-error-callback="onError"></div>';
    }

    public function validate(): bool
    {
        $data = [
            'secret' => EnvManager::getInstance()->getValue("HCAPTCHA_SECRET_KEY"),
            'response' => $_POST['h-captcha-response'],
        ];
        $verify = curl_init();
        curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
        curl_setopt($verify, CURLOPT_POST, true);
        curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($verify);

        return json_decode($response, false, 512, JSON_THROW_ON_ERROR)->success;
    }

    public function adminForm(): void
    {
        $siteKey = EnvManager::getInstance()->getValue("HCAPTCHA_SITE_KEY");
        $secretKey = EnvManager::getInstance()->getValue("HCAPTCHA_SECRET_KEY");

        $infoCaptcha = LangManager::translate('core.security.free_key');

        echo <<<HTML
<script>
    const hcaptcha = () => {
        let parent = document.getElementById("security-content-wrapper");

        let divWrapper = document.createElement("div");
        divWrapper.setAttribute("class", "grid-2");

        let divPrepend = document.createElement("div");
        divPrepend.setAttribute("class", "col-md-6");

        let divPrepend2 = document.createElement("div");
        divPrepend2.setAttribute("class", "col-md-6");

        let divInfoCaptcha = document.createElement("p");
        divInfoCaptcha.innerHTML += '$infoCaptcha <a class="link" href="https://www.hcaptcha.com/" target="_blank">https://www.hcaptcha.com/</a>';

        let divFormGroupSiteKey = document.createElement("div");
        divFormGroupSiteKey.setAttribute("class", "form-group");

        let divFormGroupSecretKey = document.createElement("div");
        divFormGroupSecretKey.setAttribute("class", "form-group");

        let labelSiteKey = document.createElement("label");
        labelSiteKey.innerHTML += "<label>Site Key :</label>";

        let labelSecreteKey = document.createElement("label");
        labelSecreteKey.innerHTML += "<label>Secret Key :</label>";

        let inputSiteKey = document.createElement("input");
        inputSiteKey.setAttribute("value", '$siteKey');
        inputSiteKey.setAttribute("placeholder", "Site-Key")
        inputSiteKey.setAttribute("type", "text")
        inputSiteKey.setAttribute("name", "captcha_hcaptcha_site_key");
        inputSiteKey.setAttribute("class", "input");
        inputSiteKey.setAttribute("required", "true");

        let inputSecretKey = document.createElement("input");
        inputSecretKey.setAttribute("value", '$secretKey');
        inputSecretKey.setAttribute("placeholder", "Secret-Key")
        inputSecretKey.setAttribute("type", "text")
        inputSecretKey.setAttribute("name", "captcha_hcaptcha_secret_key");
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
            "HCAPTCHA_SITE_KEY",
            FilterManager::filterInputStringPost('captcha_hcaptcha_site_key', orElse: ''),
        );
        EnvManager::getInstance()->setOrEditValue(
            "HCAPTCHA_SECRET_KEY",
            FilterManager::filterInputStringPost('captcha_hcaptcha_secret_key', orElse: ''),
        );
    }
}

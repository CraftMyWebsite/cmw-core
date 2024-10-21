<?php

namespace CMW\Implementation\Core\Core;

use CMW\Interface\Core\ICaptcha;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Lang\LangManager;
use CMW\Utils\Client;
use function file_get_contents;
use function json_decode;
use const JSON_THROW_ON_ERROR;

class CaptchaTurnstileImplementation implements ICaptcha
{
    public function getName(): string
    {
        return 'Cloudflare Turnstile';
    }

    public function getCode(): string
    {
        return 'turnstile';
    }

    public function show(): void
    {
        echo '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';
        echo '<div class="cf-turnstile" data-sitekey="' . EnvManager::getInstance()->getValue('TURNSTILE_SITE_KEY') . '"></div>';
    }

    public function validate(): bool
    {
        $turnstileResponse = $_POST['cf-turnstile-response'];

        $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
        $data = [
            'secret' => EnvManager::getInstance()->getValue('TURNSTILE_SECRET_KEY'),
            'response' => $turnstileResponse,
            'remoteip' => Client::getIp(),
        ];

        $options = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($data),
            ],
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        return json_decode($response, false, 512, JSON_THROW_ON_ERROR)->success;
    }

    public function adminForm(): void
    {
        $siteKey = EnvManager::getInstance()->getValue('TURNSTILE_SITE_KEY');
        $secretKey = EnvManager::getInstance()->getValue('TURNSTILE_SECRET_KEY');

        $infoCaptcha = LangManager::translate('core.security.free_key');

        echo <<<HTML

            <script>
                const turnstile = () => {
                    let parent = document.getElementById("security-content-wrapper");

                    let divWrapper = document.createElement("div");
                    divWrapper.setAttribute("class", "grid-2");

                    let divPrepend = document.createElement("div");
                    divPrepend.setAttribute("class", "col-md-6");

                    let divPrepend2 = document.createElement("div");
                    divPrepend2.setAttribute("class", "col-md-6");

                    let divInfoCaptcha = document.createElement("p");
                    divInfoCaptcha.innerHTML += '$infoCaptcha <a class="link" href="https://developers.cloudflare.com/turnstile/" target="_blank">Cloudflare Turnstile</a>';

                    let divFormGroupSiteKey = document.createElement("div");
                    divFormGroupSiteKey.setAttribute("class", "form-group");

                    let divFormGroupSecretKey = document.createElement("div");
                    divFormGroupSecretKey.setAttribute("class", "form-group");

                    let labelSiteKey = document.createElement("label");
                    labelSiteKey.innerHTML += "<label>Site Key :</label>";

                    let labelSecretKey = document.createElement("label");
                    labelSecretKey.innerHTML += "<label>Secret Key :</label>";

                    let inputSiteKey = document.createElement("input");
                    inputSiteKey.setAttribute("value", '$siteKey');
                    inputSiteKey.setAttribute("placeholder", "Site-Key")
                    inputSiteKey.setAttribute("type", "text")
                    inputSiteKey.setAttribute("name", "captcha_turnstile_site_key");
                    inputSiteKey.setAttribute("class", "input");
                    inputSiteKey.setAttribute("required", "true");

                    let inputSecretKey = document.createElement("input");
                    inputSecretKey.setAttribute("value", '{$secretKey}');
                    inputSecretKey.setAttribute("placeholder", "Secret-Key")
                    inputSecretKey.setAttribute("type", "text")
                    inputSecretKey.setAttribute("name", "captcha_turnstile_secret_key");
                    inputSecretKey.setAttribute("class", "input");
                    inputSecretKey.setAttribute("required", "true");

                    parent.append(divWrapper);

                    divWrapper.append(divPrepend);
                    divPrepend.append(divFormGroupSiteKey);
                    divFormGroupSiteKey.append(labelSiteKey);
                    divFormGroupSiteKey.append(inputSiteKey);

                    divWrapper.append(divPrepend2);
                    divPrepend2.append(divFormGroupSecretKey);
                    divFormGroupSecretKey.append(labelSecretKey);
                    divFormGroupSecretKey.append(inputSecretKey);

                    parent.append(divInfoCaptcha);
                }
            </script>
            HTML;
    }

    public function adminFormPost(): void
    {
        EnvManager::getInstance()->setOrEditValue(
            'TURNSTILE_SITE_KEY',
            FilterManager::filterInputStringPost('captcha_turnstile_site_key'),
        );
        EnvManager::getInstance()->setOrEditValue(
            'TURNSTILE_SECRET_KEY',
            FilterManager::filterInputStringPost('captcha_turnstile_secret_key'),
        );
    }
}

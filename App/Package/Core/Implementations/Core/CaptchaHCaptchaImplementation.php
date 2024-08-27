<?php

namespace CMW\Implementation\Core\Core;

use CMW\Interface\Core\ICaptcha;
use CMW\Manager\Env\EnvManager;
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
}

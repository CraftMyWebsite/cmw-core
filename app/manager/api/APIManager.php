<?php

namespace CMW\Manager\Api;

use CMW\Utils\Utils;
use CurlHandle;

class APIManager
{

    private const ENV_KEY = "api password";
    private const HEADER_KEY = "X-CMW-ACCESS";

    public function __construct()
    {

    }

    public function __invoke(): void
    {
        self::getPassword();
    }

    public static function generatePassword(): string
    {
        return uniqid("cmw-api", true);
    }

    private static function hashPassword($password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    private static function getPassword(): string
    {
        if (!Utils::getEnv()->valueExist(self::ENV_KEY)) {
            Utils::getEnv()->addValue(self::ENV_KEY, self::generatePassword());
        }

        $password = Utils::getEnv()->getValue(self::ENV_KEY);

        return self::hashPassword($password);
    }

    private static function generateHeader(string $url): CurlHandle|bool
    {
        $curlHandle = curl_init($url);
        $passwordAccess = self::getPassword();
        $headerAccess = self::HEADER_KEY;
        $headers = array(
            "$headerAccess : $passwordAccess"
        );


        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, true);

        return $curlHandle;
    }


    public static function postRequest(string $url, array $data = []): string|false
    {
        //todo verif if url is real URL.

        $curlHandle = self::generateHeader($url);

        $data = json_encode($data, JSON_THROW_ON_ERROR);
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($curlHandle);
        curl_close($curlHandle);
        return $response;
    }

    public static function getRequest(string $url): string|false
    {
        //todo verif if url is real URL.

        $curlHandle = self::generateHeader($url);

        $response = curl_exec($curlHandle);
        curl_close($curlHandle);
        return $response;
    }

    public static function sendSecureJson(string $message, int $code = 200, array $data = array()): bool|string
    {
        header("Content-Type: application/json; charset=UTF-8");
        return json_encode(array(
            "message" => $message,
            "code" => $code,
            "data" => $data
        ), JSON_THROW_ON_ERROR);
    }

    public static function canRequestWebsite(): bool
    {
        $headers = $_SERVER;

        $key = $headers[self::HEADER_KEY] ?? null;

        if (is_null($key)) {
            return false;
        }

        return self::verifyPassword($key);
    }

    private static function verifyPassword($hashedPass): bool
    {
        return password_verify(Utils::getEnv()->getValue(self::ENV_KEY), $hashedPass);
    }


}
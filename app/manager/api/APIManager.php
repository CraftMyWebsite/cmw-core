<?php

namespace CMW\Manager\Api;

use CMW\Utils\Utils;
use CurlHandle;
use JsonException;

class APIManager
{

    private const ENV_KEY = "api_password";
    private const HEADER_KEY = "X-CMW-ACCESS";
    private const HTTP_HEADER_KEY = "HTTP_X_CMW_ACCESS";

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

    private static function getSecureHeader(): string
    {
        return self::HEADER_KEY . ": " . self::getPassword();
    }

    private static function generateHeader(string $url, $secure, bool $isPost = false): CurlHandle|bool
    {
        $curlHandle = curl_init($url);
        $passwordAccess = self::getPassword();
        $headerAccess = self::HEADER_KEY;
        $headers = $secure
            ? array(self::getSecureHeader())
            : array();

        $isPost === true ? $headers[] .= 'Content-Type: application/x-www-form-urlencoded' : '';

        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, true);

        return $curlHandle;
    }


    public static function postRequest(string $url, array $data = [], $secure = true): string|false
    {
        //todo verif if url is real URL.

        // TODO Add retry function

        $curlHandle = self::generateHeader($url, $secure, true);

        $parsedData = http_build_query($data);
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $parsedData);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 5);
        curl_setopt($curlHandle, CURLOPT_ENCODING, "gzip");
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curlHandle);
        curl_close($curlHandle);
        return $response;
    }

    public static function getRequest(string $url, $secure = true): string|false
    {
        //todo verif if url is real URL.

        // TODO Add retry function

        $curlHandle = self::generateHeader($url, $secure);

        $response = curl_exec($curlHandle);
        curl_close($curlHandle);
        return $response;
    }

    public static function createResponse(string $message = "", int $code = 200, array $data = array(), $secure = true): bool|string
    {

        header("Content-Type: application/json; charset=UTF-8");
        if ($secure) {
            header(self::getSecureHeader());
        }
        try {
            return json_encode(array(
                "message" => $message,
                "code" => $code,
                "data" => $data
            ), JSON_THROW_ON_ERROR);
        } catch (JsonException) {
        }
    }

    public static function canRequestWebsite($headerKey = self::HTTP_HEADER_KEY, $key = self::ENV_KEY): bool
    {
        $receivedKey = $_SERVER[$headerKey] ?? null;

        if (is_null($receivedKey)) {
            return false;
        }

        return self::verifyPassword($receivedKey, $key);
    }

    private static function verifyPassword($hashedPass, $key): bool
    {
        return password_verify(Utils::getEnv()->getValue($key), $hashedPass);
    }


}
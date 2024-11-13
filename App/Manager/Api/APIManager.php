<?php

namespace CMW\Manager\Api;

use CMW\Manager\Env\EnvManager;
use CMW\Utils\ArrayFormatter;
use CMW\Utils\Log;
use CurlHandle;
use JsonException;

class APIManager
{
    private const string ENV_KEY = 'api_password';
    private const string HEADER_KEY = 'X-CMW-ACCESS';
    private const string HTTP_HEADER_KEY = 'HTTP_X_CMW_ACCESS';

    public function __construct()
    {
    }


    public function __invoke(): void
    {
        self::getPassword();
    }

    public static function generatePassword(): string
    {
        return uniqid('cmw-api', true);
    }

    private static function hashPassword($password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    private static function getPassword(): string
    {
        if (!EnvManager::getInstance()->valueExist(self::ENV_KEY)) {
            EnvManager::getInstance()->addValue(self::ENV_KEY, self::generatePassword());
        }

        $password = EnvManager::getInstance()->getValue(self::ENV_KEY);

        return self::hashPassword($password);
    }

    private static function generateHeader(string $url, $secure, bool $isPost = false, string $cmwlToken = null): CurlHandle|bool
    {
        $curlHandle = curl_init($url);
        $passwordAccess = self::getPassword();
        $headerAccess = self::HEADER_KEY;
        $headers = $secure
            ? [self::HEADER_KEY . ': ' . $cmwlToken]
            : [];

        $isPost === true ? $headers[] .= 'Content-Type: application/x-www-form-urlencoded' : '';

        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, true);

        return $curlHandle;
    }

    public static function postRequest(string $url, array $data = [], $secure = true, string $cmwlToken = null): string|false
    {
        // todo verif if url is real URL.

        // TODO Add retry function

        $curlHandle = self::generateHeader($url, $secure, true, cmwlToken: $cmwlToken);

        $parsedData = http_build_query($data);
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $parsedData);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 5);
        curl_setopt($curlHandle, CURLOPT_ENCODING, 'gzip');
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curlHandle);
        curl_close($curlHandle);
        return $response;
    }

    public static function getRequest(string $url, $secure = true, string $cmwlToken = null): string|false
    {
        // todo verif if url is real URL.

        // TODO Add retry function

        $curlHandle = self::generateHeader($url, $secure, cmwlToken: $cmwlToken);

        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 3);  // 3sec timeout
        $response = curl_exec($curlHandle);
        curl_close($curlHandle);
        return $response;
    }

    public static function createResponse(string $message = '', int $code = 200, array $data = [], $secure = true, string $cmwlToken = null): bool|string
    {
        header('Content-Type: application/json; charset=UTF-8');
        if ($secure && !is_null($cmwlToken)) {
            header(self::HEADER_KEY . ': ' . $cmwlToken);
        }

        if (empty($data)) {
            $code = 204;
        }

        if (!empty($message)) {
            $json['message'] = $message;
        }

        $json = [
            'code' => $code,
            'data' => $data,
        ];

        try {
            return json_encode(ArrayFormatter::convertToArray($json), JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return false;
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
        return password_verify(EnvManager::getInstance()->getValue($key), $hashedPass);
    }

    /**
     * @param array $data
     * @param string $prefix
     * @return string
     * @desc
     * <p>This method build query like http_build_query() but we are not ignoring null,
     * empty string or empty list.</p>
     */
    public static function buildQuery(array $data, string $prefix = ''): string
    {
        $query = [];

        foreach ($data as $key => $value) {
            if ($prefix !== '') {
                $key = $prefix . '[' . $key . ']';
            }

            if (is_array($value)) {
                if (empty($value)) {
                    $query[] = urlencode($key) . '=';
                } else {
                    $query[] = self::buildQuery($value, $key);
                }
            } elseif (is_null($value)) {
                $query[] = urlencode($key) . '=';
            } elseif ($value === '') {
                $query[] = urlencode($key) . '=';
            } else {
                $query[] = urlencode($key) . '=' . urlencode((string)$value);
            }
        }

        return implode('&', $query);
    }
}

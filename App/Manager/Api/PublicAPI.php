<?php

namespace CMW\Manager\Api;

use CMW\Manager\Env\EnvManager;
use JsonException;

class PublicAPI
{
    /**
     * @return string
     * @desc Get the API URL
     */
    public static function getUrl(): string
    {
        return EnvManager::getInstance()->getValue("APIURL") . "/v" . self::$currentApiVersion . "/";
    }

    private static int $currentApiVersion = 1;

    /**
     * @return string|null
     * @desc Can be null if installation not started.
     */
    private static function getWebsiteKeyEncoded(): ?string
    {
        $key = EnvManager::getInstance()->getValue("CMW_KEY");
        if (!is_null($key)) {
            return base64_encode($key);
        }
        return null;
    }

    /**
     * @param string $url
     * @param array $data
     * @return array
     * @desc Use Stream context to post data
     */
    public static function postData(string $url, array $data = []): mixed
    {
        $url = self::getUrl() . $url;

        $url .= '&lang=' . EnvManager::getInstance()->getValue('LOCALE');
        $url .= '&website_key=' . self::getWebsiteKeyEncoded();

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => "POST",
                'ignore_errors' => true,
                'content' => http_build_query($data),
            ],
        ];

        $context = stream_context_create($options);

        try {
            return json_decode(file_get_contents($url, false, $context), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return [];
        }
    }

    /**
     * @param string $url
     * @return mixed
     * @desc Use Stream context to get data.
     */
    public static function getData(string $url): mixed
    {
        $url = self::getUrl() . $url;

        $url .= '&website_key=' . self::getWebsiteKeyEncoded();
        $url .= '&lang=' . EnvManager::getInstance()->getValue('LOCALE');

        $options = [
            'http' => [
                'method' => "GET",
                'ignore_errors' => true,
            ],
        ];

        $context = stream_context_create($options);

        try {
            return json_decode(file_get_contents($url, false, $context), JSON_THROW_ON_ERROR, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return [];
        }
    }

    /**
     * @param string $url
     * @return mixed
     * @desc Use Stream context to put data.
     */
    public static function putData(string $url): mixed
    {
        $url = self::getUrl() . $url;

        $url .= '&website_key=' . self::getWebsiteKeyEncoded();
        $url .= '&lang=' . EnvManager::getInstance()->getValue('LOCALE');

        $options = [
            'http' => [
                'method' => "PUT",
                'ignore_errors' => true,
            ],
        ];

        $context = stream_context_create($options);

        try {
            return json_decode(file_get_contents($url, false, $context), JSON_THROW_ON_ERROR, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return [];
        }
    }

}
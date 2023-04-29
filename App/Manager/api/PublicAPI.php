<?php

namespace CMW\Manager\Api;

use CMW\Utils\Utils;
use JsonException;

class PublicAPI
{
    /**
     * @return string
     * @desc Get the API URL
     */
    public static function getUrl(): string
    {
        return Utils::getEnv()->getValue("APIURL");
    }

    private static function getWebsiteKey(): string
    {
        return Utils::getEnv()->getValue("CMW_KEY");
    }

    /**
     * @param string $url
     * @param array $data
     * @param bool $useWebsiteKey
     * @return array
     * @desc Use Stream context to post data
     */
    public static function postData(string $url, array $data = [], bool $useWebsiteKey = true): mixed
    {
        $url = self::getUrl()  . '/' . $url;

        $data['website_key'] = $useWebsiteKey ? base64_encode(self::getWebsiteKey()) : [];

        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' =>  "POST",
                'ignore_errors' => true,
                'content' => http_build_query($data)
            )
        );

        $context = stream_context_create($options);

        try {
            return json_decode(file_get_contents($url, false, $context), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return [];
        }
    }

    /**
     * @param string $url
     * @param bool $useWebsiteKey
     * @param bool $useLang
     * @return array
     * @desc Use Stream context to get data.
     */
    public static function getData(string $url, bool $useWebsiteKey = true, bool $useLang = true): array
    {
        $url = self::getUrl()  . '/' . $url;

        if ($useWebsiteKey){
            $url .= '&website_key=' . base64_encode(self::getWebsiteKey());
        }

        if ($useLang){
            $url .='&Lang=' . Utils::getEnv()->getValue('LOCALE');
        }

        $options = array(
            'http' => array(
                'method' =>  "GET",
                'ignore_errors' => true,
            )
        );

        $context = stream_context_create($options);

        try {
            return json_decode(file_get_contents($url, false, $context), JSON_THROW_ON_ERROR, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return [];
        }
    }

}
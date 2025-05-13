<?php

namespace CMW\Manager\Http;


class Url
{
    /**
     * @param string $url
     * @return string
     */
    public static function getUrl(string $url): string
    {
        return str_replace(['http://', 'https://'], '', $url);
    }

    /**
     * @param string $url
     * @return string
     */
    public static function getDomain(string $url): string
    {
        return parse_url($url, PHP_URL_HOST);
    }

    /**
     * @param string $url
     * @return string
     */
    public static function getPath(string $url): string
    {
        return parse_url($url, PHP_URL_PATH);
    }

    /**
     * @param string $url
     * @return string
     */
    public static function getQuery(string $url): string
    {
        return parse_url($url, PHP_URL_QUERY);
    }

    /**
     * @param string $url
     * @return bool
     */
    public static function hasQuery(string $url): bool
    {
        return parse_url($url, PHP_URL_QUERY) !== null;
    }

    /**
     * @param string $url
     * @return string
     */
    public static function getFragment(string $url): string
    {
        return parse_url($url, PHP_URL_FRAGMENT);
    }
}
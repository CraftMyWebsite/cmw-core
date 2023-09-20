<?php

namespace CMW\Manager\Filter;

class FilterManager
{
    /**
     * @param string $url
     * @return string
     * @desc Filter complete url
     */
    public static function filterUrl(string $url): string
    {
        return preg_replace('/https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()!@:%_\+.~#?&\/\/=]*)/', '', $url);
    }

    /**
     * @param string $data
     * @param int $maxLength
     * @return string
     * @desc Securely filter data with maxlength parameter
     */
    public static function filterData(string $data, int $maxLength = 128): string
    {
        $data = trim(preg_replace("/<\?.*\?>/", '', $data)); //Remove scripts tags
        $data = mb_substr($data, 0, $maxLength);
        return filter_var($data, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    /**
     * @param int $maxLength
     * @param string ...$values
     * @return array
     * @desc Securely filter multiples data with maxlength parameter
     */
    public static function filterMultiplesData(int $maxLength = 128, string ...$values): array
    {
        $toReturn = [];
        foreach ($values as $value) {
            $toReturn[] = self::filterData($value, $maxLength);
        }
        return $toReturn;
    }

    /**
     * @param string $data
     * @param int $maxLength
     * @return string
     * @desc Securely filter data with maxlength parameter => optimized for strings
     */
    public static function filterInputStringPost(string $data, int $maxLength = 255): string
    {
        return mb_substr(trim(filter_input(INPUT_POST, $data, FILTER_SANITIZE_FULL_SPECIAL_CHARS)), 0, $maxLength);
    }

    /**
     * @param string $data
     * @param int $maxLength
     * @return int
     * @desc Securely filter data with maxlength parameter => optimized for strings
     */
    public static function filterInputIntPost(string $data, int $maxLength = 128): int
    {
        return mb_substr(trim(filter_input(INPUT_POST, $data, FILTER_SANITIZE_NUMBER_INT)), 0, $maxLength);
    }

    /**
     * @param string $data
     * @param int $maxLength
     * @return string
     * @desc Securely filter data with maxlength parameter => optimized for strings
     */
    public static function filterInputStringGet(string $data, int $maxLength = 128): string
    {
        return mb_substr(trim(filter_input(INPUT_GET, $data, FILTER_SANITIZE_FULL_SPECIAL_CHARS)), 0, $maxLength);
    }

    /**
     * @param string $data
     * @param int $maxLength
     * @return int
     * @desc Securely filter data with maxlength parameter => optimized for strings
     */
    public static function filterInputIntGet(string $data, int $maxLength = 128): int
    {
        return mb_substr(trim(filter_input(INPUT_GET, $data, FILTER_SANITIZE_NUMBER_INT)), 0, $maxLength);
    }

    /**
     * @param string $mail
     * @return bool
     * @desc We are checking if this string is an email address. <b>Please filter before use</b>
     */
    public static function isEmail(string $mail): bool
    {
        return filter_var($mail, FILTER_VALIDATE_EMAIL);
    }
}
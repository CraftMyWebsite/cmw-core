<?php

namespace CMW\Manager\Filter;

use function filter_input;
use function is_null;
use function trim;
use const FILTER_UNSAFE_RAW;
use const INPUT_POST;

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
     * @param int $filter
     * @return string
     * @desc Securely filter data with maxlength parameter and custom filter, see @link
     * @link https://www.php.net/manual/en/filter.filters.sanitize.php
     */
    public static function filterData(string $data, int $maxLength = 128, int $filter = FILTER_UNSAFE_RAW): string
    {
        $data = trim(preg_replace('/<\?.*\?>/', '', $data));  // Remove scripts tags
        $data = mb_substr($data, 0, $maxLength);
        return filter_var($data, $filter);
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
     * @param int|null $maxLength
     * @param mixed|null $orElse
     * @return mixed
     * @desc Securely filter data with maxlength parameter => optimized for strings.
     * <p>If <b>orElse</b> parameter is used, you can return anything you want if value is not set or null.</p>
     * <p>If $maxLength is NULL, we are ignoring this parameter.</p>
     */
    public static function filterInputStringPost(string $data, ?int $maxLength = 255, mixed $orElse = false): mixed
    {
        if ((!$orElse) && !isset($_POST[$data]) && !is_null($_POST[$data])) {
            return $orElse;
        }

        $formattedData = trim(filter_input(INPUT_POST, $data, FILTER_UNSAFE_RAW));

        if (!is_null($maxLength)) {
            return mb_substr($formattedData, 0, $maxLength);
        }

        return $formattedData;
    }

    /**
     * @param string $data
     * @param int $maxLength
     * @return int
     * @desc Securely filter data with maxlength parameter => optimized for strings
     */
    public static function filterInputIntPost(string $data, int $maxLength = 128): int
    {
        return (int)mb_substr(trim(filter_input(INPUT_POST, $data, FILTER_SANITIZE_NUMBER_INT)), 0, $maxLength);
    }

    /**
     * @param string $data
     * @param int $maxLength
     * @return string
     * @desc Securely filter data with maxlength parameter => optimized for strings
     */
    public static function filterInputStringGet(string $data, int $maxLength = 128): string
    {
        return mb_substr(trim(filter_input(INPUT_GET, $data, FILTER_UNSAFE_RAW)), 0, $maxLength);
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

    /**
     * @param string $data
     * @return string
     * @desc Prepare string for sql. Fix #039 for apostrophe.
     */
    public static function prepareSqlInsert(string $data): string
    {
        return html_entity_decode(trim($data), ENT_QUOTES, 'UTF-8');
    }
}

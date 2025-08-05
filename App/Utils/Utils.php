<?php

namespace CMW\Utils;

use function implode;
use function lcfirst;
use function preg_replace;
use function str_replace;
use function str_shuffle;
use function strtolower;
use function substr;
use function time;
use function ucwords;

/**
 * Class: @Utils
 * @package Utils
 * @author CraftMywebsite <contact@craftmywebsite.fr>
 * @version 1.0
 */
class Utils
{
    public static function isValuesEmpty(array $array, string ...$values): bool
    {
        foreach ($values as $value) {
            if (empty($array[$value])) {
                return true;
            }
        }

        return false;
    }

    public static function containsNullValue(?string ...$values): bool
    {
        foreach ($values as $value) {
            if (is_null($value)) {
                return true;
            }
        }

        return false;
    }

    public static function normalizeForSlug($text, $encode = 'UTF-8'): string
    {
        $text = mb_strtolower(trim(self::removeAccents($text, $encode)));
        $text = preg_replace('/\s+/', '-', $text);
        $text = preg_replace('/(-)\1+/', '$1', $text);
        $text = preg_replace('/[^A-z\-\d]/', '', $text);
        if ($text[strlen($text) - 1] === '-') {
            $text = substr_replace($text, '', -1);
        }
        return $text;
    }

    public static function removeAccents($text, $encode = 'UTF-8'): string
    {
        $text = preg_replace('/[\'"^]/', '-', $text);
        return preg_replace('~&([A-z]{1,2})(acute|cedil|caron|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($text, ENT_QUOTES, $encode));
    }

    public static function addIfNotNull(array &$array, mixed $value): void
    {
        if (!is_null($value)) {
            $array[] = $value;
        }
    }

    public static function filterInput(string ...$values): array
    {
        $toReturn = [];
        foreach ($values as $value) {
            $toReturn[] = filter_input(INPUT_POST, $value);
        }

        return $toReturn;
    }

    /**
     * @param int $l
     * @return string
     * @desc Return a string ID
     */
    public static function genId(int $l = 5): string
    {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 10, $l);
    }

    /**
     * @return string
     */
    public static function generateUUID(): string
    {
        $toReturn = [];

        for ($i = 0; $i < 8; $i++) {
            $toReturn[] = self::genId(4);
        }

        return implode("-", $toReturn) . '-' . time();
    }

    /**
     * @param int $length
     * @return string
     */
    public static function generateRandomNumber(int $length): string
    {
        return substr(str_shuffle("0123456789"), 0, $length);
    }

    /**
     * @param string $data
     * @return string
     * @desc Replace CamelCase to snake_case. Ex: blaBla => bla_bla
     * @deprecated use Str::camelToSnakeCase()
     */
    public static function camelToSnakeCase(string $data): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $data));
    }

    /**
     * @param string $data
     * @return string
     * @desc Replace snake_case to CamelCase. Ex: bla_bla => blaBla
     * @deprecated use Str::snakeToCamelCase()
     */
    public static function snakeToCamelCase(string $data): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $data))));
    }
}

<?php

namespace CMW\Utils;

use JetBrains\PhpStorm\NoReturn;
use JsonException;

require("EnvBuilder.php");

/**
 * Class: @Utils
 * @package Utils
 * @author CraftMywebsite <contact@craftmywebsite.fr>
 * @version 1.0
 */
class Utils
{
    private static EnvBuilder $env;

    public function __construct()
    {
        self::$env ??= new EnvBuilder();
        $_SESSION["alerts"] ??= array();
    }

    public static function getEnv(): EnvBuilder
    {
        return self::$env;
    }

    public static function isValuesEmpty(array $array, string ...$values): bool
    {
        foreach ($values as $value) {
            if (empty($array[$value])) {
                return true;
            }
        }

        return false;
    }

    public static function hasOneNullValue(string ...$values): bool
    {
        foreach ($values as $value) {
            if (is_null($value)) {
                return true;
            }
        }

        return false;
    }

    public static function normalizeForSlug($text, $encode = "UTF-8"): string
    {
        $text = mb_strtolower(trim(self::removeAccents($text, $encode)));
        $text = preg_replace("/\s+/", "-", $text);
        $text = preg_replace("/(-)\\1+/", "$1", $text);
        $text = preg_replace("/[^A-z\-\d]/", "", $text);
        if ($text[strlen($text) - 1] === '-') {
            $text = substr_replace($text, "", -1);
        }
        return $text;
    }

    public static function removeAccents($text, $encode = "UTF-8"): string
    {
        $text = preg_replace("/['\"^]/", "-", $text);
        return preg_replace("~&([A-z]{1,2})(acute|cedil|caron|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i", "$1", htmlentities($text, ENT_QUOTES, $encode));
    }

    public static function addIfNotNull(array &$array, mixed $value): void
    {
        if (!is_null($value)) {
            $array[] = $value;
        }
    }

    public static function filterInput(string ...$values): array
    {
        $toReturn = array();
        foreach ($values as $value) {
            $toReturn[] = filter_input(INPUT_POST, $value);
        }

        return $toReturn;
    }

    public static function deleteDirectory($dir): bool
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            if (!self::deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }

        return rmdir($dir);
    }

    /**
     * @throws JsonException
     */
    public static function getPackageInfo($package): mixed
    {
        $jsonFile = file_get_contents("app/package/$package/infos.json");
        return json_decode($jsonFile, true, 512, JSON_THROW_ON_ERROR);
    }

    #[NoReturn] public static function sendErrorCode($err = 404): void
    {
        http_response_code($err);
        die();
    }

    /**
     * @param $l
     * @return string
     * @desc Return a string ID
     */
    public static function genId($l = 5): string
    {
        return substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 10, $l);
    }
}
<?php

namespace CMW\Utils;

use JetBrains\PhpStorm\NoReturn;

require("EnvBuilder.php");

class Utils
{
    private static EnvBuilder $env;

    public function __construct()
    {
        self::$env ??= new EnvBuilder();
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

    public static function removeAccents($text, $encode = "UTF-8"): string
    {
        $text = preg_replace("/['\"^]/", "-", $text);
        return preg_replace("~&([A-z]{1,2})(acute|cedil|caron|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i", "$1", htmlentities($text, ENT_QUOTES, $encode));
    }

    public static function normalizeForSlug($text, $encode = "UTF-8"): string
    {
        $text = mb_strtolower(trim(self::removeAccents($text, $encode)));
        $text = preg_replace("/\s'+/", "-", $text);
        $text = preg_replace("/(-)\\1+/", "$1", $text);
        $text = preg_replace("/[^A-z\-\d]/", "", $text);
        if ($text[strlen($text) - 1] === '-') {
            $text = substr_replace($text, "", -1);
        }
        return $text;
    }

    #[NoReturn] public static function sendErrorCode($err = 404): void
    {
        http_response_code($err);
        die();
    }
}
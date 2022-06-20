<?php

namespace CMW\Utils;

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
}
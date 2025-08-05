<?php

namespace CMW\Utils;

use function array_map;
use function lcfirst;
use function preg_replace;
use function str_replace;
use function strtolower;
use function ucwords;

class Str
{

    public static function length(string|null $string = null): int
    {
        return mb_strlen($string ?? '', 'UTF-8');
    }

    public static function lower(string|null $string = null): string
    {
        return mb_strtolower($string ?? '', 'UTF-8');
    }

    public static function ltrim(string $string, string $trim = ' '): string
    {
        return preg_replace('!^(' . preg_quote($trim) . ')+!', '', $string);
    }

    public static function split(string|array|null $string, string $separator = ',', int $length = 1): array
    {
        if (is_array($string)) {
            return $string;
        }

        $string ??= '';

        $parts = array_map(static fn($s) => trim($s), explode($separator, $string));

        return array_filter($parts, static fn($p) => static::length($p) >= $length);
    }

    /**
     * @param string $data
     * @return string
     * @desc Replace CamelCase to snake_case. Ex: blaBla => bla_bla
     */
    public static function camelToSnakeCase(string $data): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $data));
    }

    /**
     * @param string $data
     * @return string
     * @desc Replace snake_case to CamelCase. Ex: bla_bla => blaBla
     */
    public static function snakeToCamelCase(string $data): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $data))));
    }

    /**
     * @param string|null $value
     * @return string
     */
    public static function kebab(string|null $value = null): string
    {
        return static::snake($value, '-');
    }

    /**
     * @param string|null $value
     * @param string $delimiter
     * @return string
     */
    public static function snake(string|null $value = null, string $delimiter = '_'): string
    {
        if (ctype_lower($value) === false) {
            $value = preg_replace('/\s+/u', '', ucwords($value));
            $value = preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value);
            $value = static::lower($value);
        }
        return $value;
    }

    /**
     * @param string|null $string
     * @return string
     */
    public static function upper(string|null $string = null): string
    {
        return mb_strtoupper($string ?? '', 'UTF-8');
    }
}
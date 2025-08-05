<?php


namespace CMW\Utils;

use function array_keys;
use function ctype_digit;
use function implode;
use const ARRAY_FILTER_USE_BOTH;

class Arr
{
    /**
     * <p>Check if the array is associative, ex:</p>
     * <code>
     *     $array = ['a' => 1, 'b' => 2];
     *     $array2 = [1 => 1, 2 => 2];
     *
     *     echo ArrayFormatter::isAssociative($array); // true
     *     echo ArrayFormatter::isAssociative($array2); // false
     * </code>
     * @param array $array
     * @return bool
     */
    public static function isAssociative(array $array): bool
    {
        return ctype_digit(implode('', array_keys($array))) === false;
    }

    /**
     * @param array $array
     * @param callable $callback
     * @param int $mode
     * @return array
     */
    public static function filter(array $array, callable $callback, int $mode = ARRAY_FILTER_USE_BOTH): array
    {
        return array_filter($array, $callback, $mode);
    }

    /**
     * @param array $array
     * @param mixed $value
     * @param bool $strict
     * @return bool
     */
    public static function contains(array $array, mixed $value, bool $strict = true): bool
    {
        return in_array($value, $array, $strict);
    }
}
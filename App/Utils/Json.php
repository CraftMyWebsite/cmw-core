<?php


namespace CMW\Utils;

use JsonException;
use function json_encode;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

class Json
{
    /**
     * @param mixed $value
     * @param int $options
     * @param int $depth
     * @return false|string
     */
    public static function encode(
        mixed $value,
        int   $options = JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        int   $depth = 512,
    ): false|string
    {
        return json_encode($value, $options, $depth);
    }

    /**
     * @param string|null $string
     * @return array
     */
    public static function decode(?string $string): array
    {
        if (empty($string)) {
            return [];
        }

        try {
            $res = json_decode($string, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            $res = null;
        }

        if (!is_array($res)) {
            return [];
        }

        return $res;
    }
}
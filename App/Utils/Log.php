<?php

namespace CMW\Utils;

class Log
{
    /**
     * @param string $data
     * @throws \JsonException
     * @desc Echo the data in the navigator console
     */
    public static function console(string $data): void
    {
        echo '<script>';
        echo 'console.log(' . json_encode($data, JSON_THROW_ON_ERROR) . ')';
        echo '</script>';
    }

    /*
     * @param mixed $arr
     * @desc Return a pretty array
     */
    public static function debug(mixed $arr): void
    {
        echo '<pre>';
        print_r($arr);
        echo '</pre>';
    }
}

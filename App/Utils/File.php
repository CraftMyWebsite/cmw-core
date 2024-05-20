<?php

namespace CMW\Utils;

class File
{
    /**
     * @param string $path
     * @param string $content
     * @param int|null $flags
     * @return bool
     * @desc Write in a specified file with multiple checks. Return <b>false</b> if the file doesn't exist or if the file is not writable
     */
    public static function write(string $path, string $content, ?int $flags = null): bool
    {
        if (!is_readable($path)) {
            return false;
        }

        if (!is_writable($path)) {
            return false;
        }

        if (is_null($flags)){
            return file_put_contents($path, $content) !== false;
        }

        return file_put_contents($path, $content, $flags) !== false;
    }

    /**
     * @param string $path
     * @return string|false
     * @desc Read the specified file with multiple checks. Return <b>false</b> if the file doesn't exist or if the file is not readable
     */
    public static function read(string $path): string|false
    {
        if (!is_readable($path)) {
            return false;
        }

        return file_get_contents($path);
    }
}

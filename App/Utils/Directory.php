<?php

namespace CMW\Utils;

class Directory
{

    public static function getFilesRecursively($dir, $extension = null, &$results = array())
    {
        $content = scandir($dir);

        foreach ($content as $_ => $value) {

            if ($value === "." || $value === "..") {
                continue;
            }

            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);

            if (!is_null($extension) && is_file($path)) {
                $pathParts = explode(DIRECTORY_SEPARATOR, $path);
                $fileParts = explode('.', end($pathParts));
                if (strtolower(end($fileParts)) !== strtolower($extension)) {
                    continue;
                }
            }

            if (!is_dir($path)) {
                $results[] = $path;
            } else {
                self::getFilesRecursively($path, $extension, $results);
            }
        }

        return $results;
    }

    public static function getElements(string $path): array
    {
        $src = is_dir($path);
        if ($src) {
            return array_diff(scandir($path), array('.', '..'));
        }

        return [];
    }

    public static function getFiles(string $path): array
    {
        $folder = self::getElements($path);
        if (empty($folder)) {
            return [];
        }

        $arrayToReturn = [];
        $path = (str_ends_with($path, '/')) ? $path : $path . '/';
        foreach ($folder as $element) {
            if (is_file($path . $element)) {
                $arrayToReturn[] = $element;
            }
        }

        return $arrayToReturn;
    }

    public static function getFolders(string $path): array
    {
        $folder = self::getElements($path);
        if (empty($folder)) {
            return [];
        }

        $arrayToReturn = [];
        $path = (str_ends_with($path, '/')) ? $path : $path . '/';
        foreach ($folder as $element) {
            if (is_dir($path . $element)) {
                $arrayToReturn[] = $element;
            }
        }

        return $arrayToReturn;
    }

    public static function delete($dir): bool
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

            if (!self::delete($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }

        return rmdir($dir);
    }

}
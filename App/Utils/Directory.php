<?php

namespace CMW\Utils;

class Directory
{
    public static function getFilesRecursively(string $dir, ?string $extension = null): array
    {
        $results = [];

        $content = scandir($dir);

        if ($content === false) {
            return $results;
        }

        foreach ($content as $value) {
            if ($value === '.' || $value === '..') {
                continue;
            }

            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);

            if (is_dir($path)) {
                self::arrayMerge($results, self::getFilesRecursively($path, $extension));
            } elseif ((is_null($extension) || self::hasMatchingExtension($path, $extension)) && is_file($path)) {
                $results[] = $path;
            }
        }

        return $results;
    }

    private static function arrayMerge(array &$result, array $subResults): void
    {
        foreach ($subResults as $res) {
            $result[] = $res;
        }
    }

    private static function hasMatchingExtension(string $path, string $extension): bool
    {
        $fileExtension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $extension = strtolower($extension);

        return $fileExtension === $extension;
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

    public static function delete(string $dir): bool
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

    /**
     * @param string ...$dirs
     * @return bool
     */
    public static function createFolders(string ...$dirs): bool
    {
        foreach ($dirs as $dir):
            if (is_dir($dir)) {
                continue;
            }

            if (!mkdir($dir, 0755, true) && !is_dir($dir)) {
                return false;
            }
        endforeach;

        return true;
    }
}

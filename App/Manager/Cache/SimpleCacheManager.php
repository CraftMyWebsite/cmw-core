<?php

namespace CMW\Manager\Cache;

use CMW\Manager\Env\EnvManager;
use CMW\Utils\Directory;
use JsonException;
use function file_exists;
use function glob;
use function is_dir;
use function is_file;
use function pathinfo;
use function unlink;
use const PATHINFO_EXTENSION;

class SimpleCacheManager
{
    private static int $cacheTime = 86400;  // Variable data ?

    private static string $dir = 'App/Storage/Cache';

    private static function getCompletePath(): string
    {
        return EnvManager::getInstance()->getValue('DIR') . self::$dir;
    }

    /**
     * @param string $fileName
     * @param string $subFolder
     * @return bool
     */
    public static function checkCache(string $fileName, string $subFolder = '/'): bool
    {
        $fileName .= '.cache';

        if ($subFolder[-1] !== '/') {
            $subFolder .= '/';
        }

        if ($subFolder[0] !== '/') {
            $subFolder = '/' . $subFolder;
        }

        return file_exists(self::getCompletePath() . $subFolder . $fileName) &&
            filemtime(self::getCompletePath() . $subFolder . $fileName) > time() - self::$cacheTime;
    }

    /**
     * @param string $fileName
     * @param string $subFolder
     * @return mixed
     * @desc Return null if the file does not exist.
     */
    public static function getCache(string $fileName, string $subFolder = '/'): mixed
    {
        $fileName .= '.cache';

        if ($subFolder[-1] !== '/') {
            $subFolder .= '/';
        }

        if ($subFolder[0] !== '/') {
            $subFolder = '/' . $subFolder;
        }

        $filePath = self::getCompletePath() . $subFolder . $fileName;

        if (!file_exists($filePath)) {
            return null;
        }

        try {
            return json_decode(file_get_contents($filePath), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            return $e;
        }
    }

    /**
     * @param mixed $value
     * @param string $fileName
     * @param string $subFolder
     * @return void
     */
    public static function storeCache(mixed $value, string $fileName, string $subFolder = '/'): void
    {
        $fileName .= '.cache';

        if ($subFolder[-1] !== '/') {
            $subFolder .= '/';
        }

        if ($subFolder[0] !== '/') {
            $subFolder = '/' . $subFolder;
        }

        Directory::createFolders(self::$dir . $subFolder);

        if (self::cacheExist($fileName, $subFolder)) {
            self::deleteSpecificCacheFile($fileName, $subFolder);
        }

        try {
            file_put_contents(self::getCompletePath() . $subFolder . $fileName, json_encode($value, JSON_THROW_ON_ERROR));
        } catch (JsonException) {
        }
    }

    /**
     * @param string $fileName
     * @param string $subFolder
     * @return bool
     */
    public static function cacheExist(string $fileName, string $subFolder = '/'): bool
    {
        $fileName .= '.cache';

        if ($subFolder[-1] !== '/') {
            $subFolder .= '/';
        }

        if ($subFolder[0] !== '/') {
            $subFolder = '/' . $subFolder;
        }

        return file_exists(self::getCompletePath() . $subFolder . $fileName);
    }

    /**
     * @param string $fileName
     * @param string $subFolder
     * @return void
     */
    public static function deleteSpecificCacheFile(string $fileName, string $subFolder = '/'): void
    {
        $fileName .= '.cache';

        if ($subFolder[-1] !== '/') {
            $subFolder .= '/';
        }

        if ($subFolder[0] !== '/') {
            $subFolder = '/' . $subFolder;
        }

        unlink(self::getCompletePath() . $subFolder . $fileName);
    }

    /**
     * @param string|null $dir
     * @return void
     * @desc This method delete all cache files (recursively)
     */
    public static function deleteAllFiles(?string $dir = null): void
    {
        $dir ??= self::getCompletePath();
        $files = glob("$dir/*");

        foreach ($files as $file) {
            if (is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === "cache") {
                unlink($file);
            } elseif (is_dir($file)) {
                self::deleteAllFiles($file);
            }
        }
    }

    /**
     * @param string $valueKey
     * @param mixed $newValue
     * @param string $fileName
     * @param string $subFolder
     * @return void
     */
    public static function editCacheValue(string $valueKey, mixed $newValue, string $fileName, string $subFolder = '/'): void
    {
        $data = self::getCache($fileName, $subFolder);

        $data[$valueKey] = $newValue;

        self::storeCache($data, $fileName, $subFolder);
    }
}

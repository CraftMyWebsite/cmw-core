<?php

namespace CMW\Manager\Cache;

use CMW\Manager\Env\EnvManager;
use CMW\Utils\Directory;
use JsonException;

class SimpleCacheManager
{
    private static int $cacheTime = 86400; //Variable data ?

    private static string $dir = "App/Storage/Cache";

    private static function getCompletePath() : string {
        return EnvManager::getInstance()->getValue('DIR') . self::$dir;
    }

    /**
     * @param string $fileName
     * @param string $subFolder
     * @return bool
     */
    public static function checkCache(string $fileName, string $subFolder = "/"): bool
    {
        $fileName .= ".cache";

        if ($subFolder[-1] !== '/'){
            $subFolder .= '/';
        }

        if ($subFolder[0] !== '/'){
            $subFolder = '/' . $subFolder;
        }

        return file_exists(self::getCompletePath() . $subFolder . $fileName) &&
            filemtime(self::getCompletePath() . $subFolder . $fileName) > time() - self::$cacheTime;
    }

    /**
     * @param string $fileName
     * @param string $subFolder
     * @return mixed
     */
    public static function getCache(string $fileName, string $subFolder = "/"): mixed
    {
        $fileName .= ".cache";

        if ($subFolder[-1] !== '/'){
            $subFolder .= '/';
        }

        if ($subFolder[0] !== '/'){
            $subFolder = '/' . $subFolder;
        }

        try {
            return json_decode(file_get_contents(self::getCompletePath() . $subFolder . $fileName), true, 512, JSON_THROW_ON_ERROR);
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
    public static function storeCache(mixed $value, string $fileName, string $subFolder = "/"): void
    {
        $fileName .= ".cache";

        if ($subFolder[-1] !== '/'){
            $subFolder .= '/';
        }

        if ($subFolder[0] !== '/'){
            $subFolder = '/' . $subFolder;
        }

        Directory::createFolders(self::$dir . $subFolder);

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
    public static function cacheExist(string $fileName, string $subFolder = "/"): bool
    {
        $fileName .= ".cache";

        if ($subFolder[-1] !== '/'){
            $subFolder .= '/';
        }

        if ($subFolder[0] !== '/'){
            $subFolder = '/' . $subFolder;
        }

        return file_exists(self::getCompletePath() . $subFolder . $fileName);
    }

    /**
     * @param string $fileName
     * @param string $subFolder
     * @return void
     */
    public static function deleteSpecificCacheFile(string $fileName, string $subFolder = "/"): void
    {
        $fileName .= ".cache";

        if ($subFolder[-1] !== '/'){
            $subFolder .= '/';
        }

        if ($subFolder[0] !== '/'){
            $subFolder = '/' . $subFolder;
        }

        unlink(self::getCompletePath() . $subFolder . $fileName);
    }
}
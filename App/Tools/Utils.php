<?php

namespace CMW\Utils;

use CMW\Model\Core\CoreModel;
use ReflectionClass;

require("EnvBuilder.php");

/**
 * Class: @Utils
 * @package Utils
 * @author CraftMywebsite <contact@craftmywebsite.fr>
 * @version 1.0
 */
class Utils
{

    public function __construct()
    {
        $_SESSION["alerts"] ??= array();
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

    public static function containsNullValue(?string ...$values): bool
    {
        foreach ($values as $value) {
            if (is_null($value)) {
                return true;
            }
        }

        return false;
    }

    public static function normalizeForSlug($text, $encode = "UTF-8"): string
    {
        $text = mb_strtolower(trim(self::removeAccents($text, $encode)));
        $text = preg_replace("/\s+/", "-", $text);
        $text = preg_replace("/(-)\\1+/", "$1", $text);
        $text = preg_replace("/[^A-z\-\d]/", "", $text);
        if ($text[strlen($text) - 1] === '-') {
            $text = substr_replace($text, "", -1);
        }
        return $text;
    }

    public static function removeAccents($text, $encode = "UTF-8"): string
    {
        $text = preg_replace("/['\"^]/", "-", $text);
        return preg_replace("~&([A-z]{1,2})(acute|cedil|caron|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i", "$1", htmlentities($text, ENT_QUOTES, $encode));
    }

    public static function addIfNotNull(array &$array, mixed $value): void
    {
        if (!is_null($value)) {
            $array[] = $value;
        }
    }

    public static function filterInput(string ...$values): array
    {
        $toReturn = array();
        foreach ($values as $value) {
            $toReturn[] = filter_input(INPUT_POST, $value);
        }

        return $toReturn;
    }

    /**
     * @param $object
     * @return array
     */
    public static function objectToArray($object): array
    {
        $reflectionClass = new ReflectionClass(get_class($object));
        $array = array();
        foreach ($reflectionClass->getProperties() as $property) {
            $array[$property->getName()] = $property->getValue($object);
        }
        return $array;
    }

    /**
     * @param int $l
     * @return string
     * @desc Return a string ID
     */
    public static function genId(int $l = 5): string
    {
        return substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 10, $l);
    }

    /**
     * @param string|null ...$values
     * @return bool
     * @deprecated please prefer {@see \CMW\Utils\Utils::containsNullValue()}
     */
    public static function hasOneNullValue(?string ...$values): bool
    {
        return self::containsNullValue(...$values);
    }

    /**
     * @return \CMW\Utils\EnvBuilder
     * @deprecated please prefer {@see \CMW\Utils\EnvBuilder::getInstance()}
     */
    public static function getEnv(): EnvBuilder
    {
        return EnvBuilder::getInstance();
    }

    /**
     * @param string $data
     * @throws \JsonException
     * @desc Echo the data in the navigator console
     * @deprecated please prefer {@see \CMW\Utils\Log::console()}
     */
    public static function debugConsole(string $data): void
    {
        Log::console($data);
    }

    /**
     * @param mixed $arr
     * @desc Return a pretty array
     * @deprecated please prefer {@see \CMW\Utils\Log::debug()}
     */
    public static function debugR(mixed $arr): void
    {
        Log::debug($arr);
    }

    /**
     * @deprecated please prefer {@see Directory::getFilesRecursively()}
     */
    public static function getFilesFromDirectory($dir, $extension = null, &$results = array())
    {
        return Directory::getFilesRecursively($dir, $extension, $results);
    }

    /**
     * @deprecated please prefer {@see Directory::getElements()}
     */
    public static function getElementsInFolder(string $path): array
    {
        return Directory::getElements($path);
    }

    /**
     * @deprecated please prefer {@see Directory::getFiles()}
     */
    public static function getFilesInFolder(string $path): array
    {
        return Directory::getFiles($path);
    }

    /**
     * @deprecated please prefer {@see Directory::getFolders()}
     */
    public static function getFoldersInFolder(string $path): array
    {
        return Directory::getFolders($path);
    }

    /**
     * @deprecated please prefer {@see Directory::delete()}
     */
    public static function deleteDirectory($dir): bool
    {
        return Directory::delete($dir);
    }

    /**
     * @return string
     * @deprecated please prefer {@see \CMW\Utils\Website::getProtocol()}
     */
    public static function getHttpProtocol(): string
    {
        return Website::getProtocol();
    }


    /**
     * @return string
     * @deprecated please prefer {@see \CMW\Utils\Website::getUrl()}
     */
    public static function getCompleteUrl(): string
    {
        return Website::getUrl();
    }

    /**
     * @return string
     * @desc Return the client ip, for local users -> 127.0.0.1, if IP not vlaid -> 0.0.0.0
     * @deprecated please prefer {@see \CMW\Utils\Website::getClientIp()}
     */
    public static function getClientIp(): string
    {
        return Website::getClientIp();
    }

    /**
     * @return string
     * @Desc Get the website name
     * @deprecated please prefer {@see \CMW\Utils\Website::getName()}
     */
    public static function getSiteName(): string
    {
        return Website::getName();
    }

    /**
     * @return string
     * @Desc Get the website description
     * @deprecated please prefer {@see \CMW\Utils\Website::getDescription()}
     */
    public static function getSiteDescription(): string
    {
        return Website::getDescription();
    }

    /**
     * @return string
     * @deprecated please prefer {@see \CMW\Utils\Website::getLogoPath()}
     */
    public static function getSiteLogoPath(): string
    {
        return Website::getLogoPath();
    }

    /**
     * @param string $targetUrl
     * @return bool
     * @desc Useful function for active navbar page
     * @deprecated please prefer {@see \CMW\Utils\Website::isCurrentPage()}
     */
    public static function isCurrentPageActive(string $targetUrl): bool
    {
        return Website::isCurrentPage($targetUrl);
    }

    /**
     * @return void
     * @deprecated please prefer {@see \CMW\Utils\Website::refresh()}
     */
    public static function refreshPage(): void
    {
        Website::refresh();
    }

    /**
     * @return string
     * @deprecated please prefer {@see \CMW\Utils\Website::getFavicon()}
     */
    public static function getFavicon(): string
    {
        return Website::getFavicon();
    }
}

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
    private static EnvBuilder $env;

    public function __construct()
    {
        self::$env ??= new EnvBuilder();
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

    public static function hasOneNullValue(?string ...$values): bool
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

    public static function deleteDirectory($dir): bool
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

            if (!self::deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }

        return rmdir($dir);
    }

    public static function getVersion(): string
    {
        return self::getEnv()->getValue("VERSION");
    }

    public static function getLatestVersion(): string
    {
        try {
            return json_decode(file_get_contents(self::getApi() . "/getCmwLatest"), false, 512, JSON_THROW_ON_ERROR)->version;
        } catch (\JsonException $e) {
        }
    }

    public static function isNewUpdateAvailable(): bool
    {
        return self::getVersion() !== self::getLatestVersion();
    }

    public static function getEnv(): EnvBuilder
    {
        return self::$env;
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
     * @param string $data
     * @throws \JsonException
     * @desc Echo the data in the navigator console
     */
    public static function debugConsole(string $data): void
    {
        echo '<script>';
        echo 'console.log(' . json_encode($data, JSON_THROW_ON_ERROR) . ')';
        echo '</script>';
    }

    /***
     * @param mixed $arr
     * @desc Return a pretty array
     */
    public static function debugR(mixed $arr): void
    {
        echo "<pre>";
        echo print_r($arr);
        echo "</pre>";
    }

    public static function getHttpProtocol(): string
    {
        return in_array($_SERVER['HTTPS'] ?? '', ['on', 1], true) ||
        ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https' ? 'https' : 'http';
    }


    public static function getCompleteUrl(): string
    {
        return self::getHttpProtocol() . "://$_SERVER[HTTP_HOST]" . self::getEnv()->getValue("PATH_SUBFOLDER");
    }

    /**
     * @return string
     * @desc Return the client ip, for local users -> 127.0.0.1
     */
    public static function getClientIp(): string
    {
        return $_SERVER['HTTP_CLIENT_IP'] ?? ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']);
    }

    /**
     * @return string
     * @desc Get the API URL
     */
    public static function getApi(): string
    {
        return self::getEnv()->getValue("APIURL");
    }

    /**
     * @return string
     * @Desc Get the website name
     */
    public static function getSiteName(): string
    {
        return (new CoreModel())->fetchOption("name");
    }

    /**
     * @return string
     * @Desc Get the website description
     */
    public static function getSiteDescription(): string
    {
        return (new CoreModel())->fetchOption("description");
    }

    public static function getSiteLogoPath(): string
    {
        $logoName = self::getFilesInFolder(self::getEnv()->getValue("DIR") . "public/uploads/logo");

        if($logoName !== []){
            return self::getEnv()->getValue("DIR") . "public/uploads/logo/" . $logoName[0];
        }

        return self::getEnv()->getValue("DIR") . "admin/resources/assets/images/logo/logo_compact.png";
    }

    public static function getElementsInFolder(string $path): array
    {
        $src = is_dir($path);
        if ($src) {
            return array_diff(scandir($path), array('.', '..'));
        }

        return [];
    }

    public static function getFilesInFolder(string $path): array
    {
        $folder = self::getElementsInFolder($path);
        if (empty($folder)) {
            return [];
        }

        $arrayToReturn = [];
        $path = (str_ends_with($path, '/')) ? $path : $path.'/';
        foreach ($folder as $element) {
            if(is_file($path.$element)) {
                $arrayToReturn[] = $element;
            }
        }

        return $arrayToReturn;
    }

    public static function getFoldersInFolder(string $path): array
    {
        $folder = self::getElementsInFolder($path);
        if (empty($folder)) {
            return [];
        }

        $arrayToReturn = [];
        $path = (str_ends_with($path, '/')) ? $path : $path.'/';
        foreach ($folder as $element) {
            if(is_dir($path.$element)) {
                $arrayToReturn[] = $element;
            }
        }

        return $arrayToReturn;
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
     * @param string $targetUrl
     * @return bool
     * @desc Useful function for active navbar page
     */
    public static function isCurrentPageActive(string $targetUrl): bool
    {

        $currentUrl = $_SERVER['REQUEST_URI'];

        return $currentUrl === $targetUrl || $currentUrl === $targetUrl . '/' || $currentUrl === $targetUrl . '#';
    }
}

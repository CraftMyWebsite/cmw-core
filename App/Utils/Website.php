<?php

namespace CMW\Utils;

use CMW\Manager\Env\EnvManager;
use CMW\Model\Core\CoreModel;
use JetBrains\PhpStorm\ExpectedValues;

class Website
{

    private static string $title;
    private static string $description;

    /**
     * @param string $title
     * @return string
     */
    public static function setTitle(string $title): string
    {
        self::$title = $title;
        return $title;
    }

    /**
     * @param bool $useSiteName
     * @return string
     */
    public static function getTitle(bool $useSiteName = true): string
    {
        $title = $useSiteName ? self::getWebsiteName() . " | " . self::$title : self::$title;

        return htmlspecialchars_decode($title, ENT_QUOTES);
    }

    /**
     * @param string $description
     * @return string
     */
    public static function setDescription(string $description): string
    {
        self::$description = $description;
        return $description;
    }

    public static function getDescription(): string
    {
        return htmlspecialchars_decode(self::$description, ENT_QUOTES);
    }


    #[ExpectedValues(values: ['https', 'http'])]
    public static function getProtocol(): string
    {
        return in_array($_SERVER['HTTPS'] ?? '', ['on', 1], true) || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https' ? 'https' : 'http';
    }

    public static function getUrl(): string
    {
        return self::getProtocol() . "://$_SERVER[HTTP_HOST]" . EnvManager::getInstance()->getValue("PATH_SUBFOLDER");
    }

    /**
     * @desc Return the client ip, for local users -> 127.0.0.1, if IP not valid -> 0.0.0.0
     * @return string
     */
    public static function getClientIp(): string
    {
        $NOT_VALID_IP = "0.0.0.0";

        $clientIp = $_SERVER['HTTP_CLIENT_IP'] ?? ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']);

        if (!filter_var($clientIp, FILTER_VALIDATE_IP)) {
            return $NOT_VALID_IP;
        }

        return $clientIp;
    }

    public static function refresh(bool $die = false): void
    {
        header("Refresh:0");

        if ($die){
            die();
        }
    }

    /**
     * @param string $targetUrl
     * @return bool
     * @desc Useful function for active navbar page
     */
    public static function isCurrentPage(string $targetUrl): bool
    {
        $currentUrl = $_SERVER['REQUEST_URI'];

        if ($targetUrl[0] === '/'){
            $targetUrl = substr($targetUrl, 0);
        }

        $targetUrl = EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . $targetUrl;

        return $currentUrl === $targetUrl || $currentUrl === $targetUrl . '/' || $currentUrl === $targetUrl . '#'; //Use Regex ?
    }

    /**
     * @param string $targetUrl
     * @return bool
     */
    public static function isContainingRoute(string $targetUrl): bool
    {
        $path = explode("/", $_SERVER["REQUEST_URI"]);
        return in_array($targetUrl, $path);
    }


    /**
     * @return string
     * @Desc Get the website name
     */
    public static function getWebsiteName(): string
    {
        return CoreModel::getInstance()->fetchOption("name");
    }

    /**
     * @return string
     * @Desc Get the website description
     */
    public static function getWebsiteDescription(): string
    {
        return CoreModel::getInstance()->fetchOption("description");
    }

    public static function getLogoPath(): string
    {
        $logoName = Directory::getFiles(EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . "Public/Uploads/logo");

        if (!empty($logoName)) {
            return EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . "Public/Uploads/Logo/" . $logoName[0];
        }

        return EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . "Admin/Resources/Assets/Images/Logo/Logo_compact.png"; //unstable...
    }

    public static function getFavicon(): string
    {
        return EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . 'Public/Uploads/Favicon/favicon.ico';
    }

}
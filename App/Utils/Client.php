<?php

namespace CMW\Utils;

use function filter_var;
use function preg_match;
use const FILTER_VALIDATE_IP;

class Client
{
    /**
     * @return string
     * @desc Return the client ip, for local users -> 127.0.0.1, if IP not valid -> 0.0.0.0
     */
    public static function getIp(): string
    {
        $clientIp = $_SERVER['HTTP_CLIENT_IP'] ?? ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']);

        if (!filter_var($clientIp, FILTER_VALIDATE_IP)) {
            return "0.0.0.0";
        }

        return $clientIp;
    }

    /**
     * @return string
     */
    public static function getUserAgent(): string
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? "NOT_DEFINED";

        if (!preg_match('#.+?[/\s][\d.]+#', $userAgent)) {
            return "INVALID";
        }

        return $userAgent;
    }

    /**
     * @return string
     */
    public static function getBrowser(): string
    {
        $userAgent = self::getUserAgent();

        $toReturn = "Other";

        $browsers = [
            '/msie/i' => 'Internet Explorer',
            '/firefox/i' => 'Firefox',
            '/safari/i' => 'Safari',
            '/chrome/i' => 'Chrome',
            '/edge/i' => 'Edge',
            '/opera/i' => 'Opera',
            '/netscape/i' => 'Netscape',
            '/maxthon/i' => 'Maxthon',
            '/konqueror/i' => 'Konqueror',
            '/mobile/i' => 'Handheld Browser',
        ];

        foreach ($browsers as $regex => $browser) {
            if (preg_match($regex, $userAgent)) {
                $toReturn = $browser;
            }
        }

        return $toReturn;
    }

    /**
     * @return string
     */
    public static function getOs(): string
    {
        $userAgent = self::getUserAgent();

        $toReturn = "Other";

        $listOs = [
            '/windows nt 11/i' => 'Windows 11',
            '/windows nt 10/i' => 'Windows 10',
            '/windows nt 6.3/i' => 'Windows 8.1',
            '/windows nt 6.2/i' => 'Windows 8',
            '/windows nt 6.1/i' => 'Windows 7',
            '/windows nt 6.0/i' => 'Windows Vista',
            '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
            '/windows nt 5.1/i' => 'Windows XP',
            '/windows xp/i' => 'Windows XP',
            '/windows nt 5.0/i' => 'Windows 2000',
            '/windows me/i' => 'Windows ME',
            '/win98/i' => 'Windows 98',
            '/win95/i' => 'Windows 95',
            '/win16/i' => 'Windows 3.11',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i' => 'Mac OS 9',
            '/linux/i' => 'Linux',
            '/ubuntu/i' => 'Ubuntu',
            '/iphone/i' => 'iPhone',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile',
        ];

        foreach ($listOs as $regex => $os) {
            if (preg_match($regex, $userAgent)) {
                $toReturn = $os;
            }
        }

        return $toReturn;
    }
}
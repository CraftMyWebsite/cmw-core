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

    public static function getUserAgent(): string
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? "NOT_DEFINED";

        if (!preg_match('#.+?[/\s][\d.]+#', $userAgent)) {
            return "INVALID";
        }

        return $userAgent;
    }
}
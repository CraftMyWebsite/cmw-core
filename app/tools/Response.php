<?php

namespace CMW\Utils;

use CMW\Manager\Response\Alert;
use JetBrains\PhpStorm\ExpectedValues;

class Response
{
    /**
     * @return Alert[]
     */
    public static function getAlerts() : array {
        return $_SESSION["alerts"];
    }

    public static function clearAlerts() : void {
        $_SESSION["alerts"] = array();
    }

    public static function sendAlert(#[ExpectedValues(["success", "error", "warning"])] string $alertType, string $title, string $message): void
    {
        $_SESSION["alerts"][] = self::createAlert($alertType, $title, $message);
    }

    public static function sendJsonMessage(string $title, string $msg, int $statusCode = 200): void
    {
        return;
    }

    private static function createAlert(string $type, string $title, string $msg): Alert
    {
        return new Alert($type, $title, $msg);
    }

}
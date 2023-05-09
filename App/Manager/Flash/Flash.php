<?php

namespace CMW\Manager\Flash;

use JetBrains\PhpStorm\ExpectedValues;

class Flash
{
    /**
     * @return Alert[]
     */
    public static function load() : array {
        if(!isset($_SESSION["alerts"])) {
            $_SESSION["alerts"] = array();
        }
        return $_SESSION["alerts"];
    }

    public static function clear() : void {
        $_SESSION["alerts"] = array();
    }

    public static function send(#[ExpectedValues(flagsFromClass: Alert::class)] string $alertType, string $title, string $message, bool $isAdmin = false): Alert
    {
        $alert = self::create($alertType, $title, $message, $isAdmin);
        $_SESSION["alerts"][] = $alert;
        return $alert;
    }

    private static function create(string $type, string $title, string $msg, bool $isAdmin): Alert
    {
        return new Alert($type, $title, $msg, $isAdmin);
    }

}
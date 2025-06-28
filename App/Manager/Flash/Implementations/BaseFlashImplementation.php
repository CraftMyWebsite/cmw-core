<?php

namespace CMW\Manager\Flash\Implementations;


use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\IFlash;
use JetBrains\PhpStorm\ExpectedValues;

class BaseFlashImplementation implements IFlash
{
    public function weight(): int
    {
        return 1;
    }

    /**
     * @return Alert[]
     */
    public static function load(): array
    {
        if (!isset($_SESSION['alerts'])) {
            $_SESSION['alerts'] = [];
        }
        return $_SESSION['alerts'];
    }

    public static function clear(): void
    {
        $_SESSION['alerts'] = [];
    }

    public static function send(#[ExpectedValues(flagsFromClass: Alert::class)] string $alertType, string $title, string $message, bool $isAdmin = false): Alert
    {
        $alert = self::create($alertType, $title, $message, $isAdmin);
        $_SESSION['alerts'][] = $alert;
        return $alert;
    }

    private static function create(string $type, string $title, string $msg, bool $isAdmin): Alert
    {
        return new Alert($type, $title, $msg, $isAdmin);
    }
}
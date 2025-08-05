<?php

namespace CMW\Manager\Flash;

use JetBrains\PhpStorm\ExpectedValues;

/**
 * @desc Interface for Flash
 */
interface IFlash
{
    /**
     * @return int
     */
    public function weight(): int;

    /**
     * @return Alert[]
     */
    public static function load(): array;

    public static function clear(): void;

    public static function send(#[ExpectedValues(flagsFromClass: Alert::class)] string $alertType, string $title, string $message, bool $isAdmin = false): Alert;
}
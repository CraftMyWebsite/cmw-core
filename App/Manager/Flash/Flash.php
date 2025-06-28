<?php

namespace CMW\Manager\Flash;

use CMW\Manager\Flash\Implementations\BaseFlashImplementation;
use CMW\Manager\Loader\Loader;
use JetBrains\PhpStorm\ExpectedValues;

class Flash
{
    private static ?IFlash $instance = null;

    public static function __callStatic(string $name, array $arguments)
    {
        if (self::$instance === null) {
            self::$instance = self::loadFlashInstance();
        }

        if (!method_exists(self::$instance, $name)) {
            throw new \BadMethodCallException("Method $name does not exist in " . get_class(self::$instance));
        }

        return self::$instance->$name(...$arguments);
    }


    private static function loadFlashInstance(): IFlash
    {
        return self::getHighestImplementation() ?? new BaseFlashImplementation();
    }

    private static function getHighestImplementation(): IFlash
    {
        $implementations = Loader::loadManagerImplementations(IFlash::class, 'Flash');

        return array_reduce($implementations, static function (?IFlash $highest, IFlash $current) {
            return ($highest === null || $current->weight() > $highest->weight()) ? $current : $highest;
        });
    }


    /**
     * @return Alert[]
     */
    public static function load(): array
    {
        if (self::$instance === null) {
            self::$instance = self::loadFlashInstance();
        }

        return self::$instance::load();
    }

    /**
     * @return void
     * <p>Clear Flash data</p>
     */
    public static function clear(): void
    {
        if (self::$instance === null) {
            self::$instance = self::loadFlashInstance();
        }

        self::$instance::clear();
    }

    /**
     * @param string $alertType
     * @param string $title
     * @param string $message
     * @param bool $isAdmin
     * @return Alert
     */
    public static function send(#[ExpectedValues(flagsFromClass: Alert::class)] string $alertType, string $title, string $message, bool $isAdmin = false): Alert
    {
        if (self::$instance === null) {
            self::$instance = self::loadFlashInstance();
        }

        return self::$instance::send($alertType, $title, $message, $isAdmin);
    }
}

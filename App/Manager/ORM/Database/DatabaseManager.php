<?php

namespace CMW\Manager\ORM\Database;

use ReflectionException;

/**
 * TODO: Recreate this class...
 */

class DatabaseManager
{

    private static ?SGBD $_sgbdInstance;

    /**
     * @throws ReflectionException
     */
    public static function setInstance(string $sgbdClass): void
    {
        $sgbd = new \ReflectionClass($sgbdClass);

        if (!isset($sgbd->getInterfaces()[SGBD::class])) {
            return; //TODO see to implement error :D
        }

        self::$_sgbdInstance = $sgbd->newInstance();
    }

    /**
     * @return SGBD|null
     */
    public static function getInstance(): ?SGBD
    {
        return self::$_sgbdInstance ?? null; //TODO see to implement error :D
    }

}
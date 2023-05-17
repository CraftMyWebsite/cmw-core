<?php

namespace CMW\Manager\Package;

abstract class GlobalObject
{

    /** @var GlobalObject[] $_instances */
    protected static array $_instances;

    /**
     * @return static Controller instance
     */
    public static function getInstance(): static
    {
        if(!isset(self::$_instances[static::class])) {
            self::$_instances[static::class] = new static;
        }

        return self::$_instances[static::class];
    }

}
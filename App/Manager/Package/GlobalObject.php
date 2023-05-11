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
        if(!isset(GlobalObject::$_instances[get_called_class()])) {
            GlobalObject::$_instances[get_called_class()] = new static;
        }

        return GlobalObject::$_instances[get_called_class()];
    }

}
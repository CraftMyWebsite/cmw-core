<?php

namespace CMW\Manager\ORM\Database;

trait getSGBDInstance
{
    public static mixed $_sgbdInstance;

    public static function getInstance(): mixed
    {
        if (!isset(self::$_sgbdInstance)) {
            self::$_sgbdInstance = self::connect();
        }

        return self::$_sgbdInstance;
    }
}
<?php

namespace CMW\Manager\Database;

use Exception;
use PDO;

class DatabaseManager
{

    protected static ?PDO $_databaseInstance = null;

    public static function getInstance(): PDO
    {
        if (!is_null(self::$_databaseInstance)) {
            return self::$_databaseInstance;
        }

        try {

            self::$_databaseInstance = new PDO("mysql:host=" . getenv("DB_HOST"), getenv("DB_USERNAME"), getenv("DB_PASSWORD"),
                array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', PDO::ATTR_PERSISTENT => true));
            self::$_databaseInstance->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            self::$_databaseInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$_databaseInstance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            /** Todo, see before if we have permissions ? */
            self::$_databaseInstance->exec("SET CHARACTER SET utf8");
            self::$_databaseInstance->exec("CREATE DATABASE IF NOT EXISTS " . getenv("DB_NAME") . ";");
            self::$_databaseInstance->exec("USE " . getenv("DB_NAME") . ";");
            return self::$_databaseInstance;
        } catch (Exception $e) {
            die("DATABASE ERROR" . $e->getMessage());
        }
    }

}
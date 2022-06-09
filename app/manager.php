<?php
namespace CMW\Model;

use Exception;
use PDO;

/**
 * Class: @manager
 * @package Core
 * @author CraftMywebsite <contact@craftmywebsite>
 * @version 1.0
 */
class manager {
    protected static $db;

    public static function dbConnect() : PDO {
        if(self::$db instanceof PDO) {
            return self::$db;
        }

        try
        {

            self::$db = new PDO("mysql:host=".getenv("DB_HOST"),getenv("DB_USERNAME"),getenv("DB_PASSWORD"),
                array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', PDO::ATTR_PERSISTENT => true));
            self::$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$db->exec("SET CHARACTER SET utf8");
            self::$db->exec("CREATE DATABASE IF NOT EXISTS ".getenv("DB_NAME").";");
            self::$db->exec("USE ".getenv("DB_NAME").";");
            return self::$db;
        }
        catch (Exception $e)
        {
            die(DATABASE_ERROR_MSG . $e->getMessage());
        }
    }
}
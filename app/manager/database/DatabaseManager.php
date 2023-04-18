<?php

namespace CMW\Manager\Database;

use CMW\Utils\Utils;
use Exception;
use PDO;

class DatabaseManager
{

    protected static ?PDO $_databaseInstance = null;

    /**
     * @return \PDO
     * @esc This instance is the main instance, <b>please use this one instead of getLiteInstance()</b> for sql queries
     */
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

    /**
     * @return \PDO
     * @desc <b>This instance is only for advanced users.</b>
     *  Why use this instance ?
     *
     *  This is instance is a simple PDO connexion without specific params and attributes.
     *
     *  -- UTF-8 SET --
     */
    public static function getLiteInstance(): PDO
    {
        $dbServername = Utils::getEnv()->getValue("DB_HOST");
        $dbUsername = Utils::getEnv()->getValue("DB_USERNAME");
        $dbPassword = Utils::getEnv()->getValue("DB_PASSWORD");
        $dbName = Utils::getEnv()->getValue("DB_NAME");
        $dbPort = Utils::getEnv()->getValue("DB_PORT");

        $db = new PDO("mysql:host=$dbServername;port=$dbPort", $dbUsername, $dbPassword);
        $db->exec("SET CHARACTER SET utf8");
        $db->exec("CREATE DATABASE IF NOT EXISTS " . $dbName . ";");
        $db->exec("USE " . $dbName . ";");

        return $db;
    }

    /**
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $name
     * @param int $port
     * @param array $options => <b>Options are PDO options, like: <e>[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]</e></b>
     * @return \PDO
     * @desc Use a custom mysql PDO instance
     */
    public static function getCustomMysqlInstance(string $host, string $username, string $password, string $name, int $port, array $options = []): PDO
    {
        $db = new PDO("mysql:host=$host;port=$port", $username, $password, $options);
        $db->exec("SET CHARACTER SET utf8");
        $db->exec("CREATE DATABASE IF NOT EXISTS " . $name . ";");
        $db->exec("USE " . $name . ";");

        return $db;
    }

    /**
     * @param string $file
     * @param array $options
     * @param bool $createMemoryDb
     * @return \PDO
     */
    public static function getCustomSqLiteInstance(string $file = "db.sqlite3", array $options = [], bool $createMemoryDb = false): PDO
    {
        if ($createMemoryDb){
            $db = new PDO("sqlite::memory:", $options);
        } else {
            $db = new PDO("sqlite:$file", $options);
        }

        return $db;
    }

    public static function isMariadb(): bool
    {
        $pdo = self::getInstance();
        $info = $pdo->query("SHOW VARIABLES like '%version%'")->fetchAll(PDO::FETCH_KEY_PAIR);

        return str_contains($info['version'], "MariaDB");
    }

}
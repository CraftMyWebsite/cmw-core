<?php

namespace CMW\Manager\Database;

use CMW\Manager\Env\EnvManager;
use Exception;
use PDO;

/**
 * TODO : Revoir complètement le Database Manager
 *  - Créer des sous classes qui sont interfacés par CMW\SGBD
 *      - getInstance()
 *  - Faire en sorte qu'on puisse choisir via Reflection l'SGBD qu'on veut (récupérable via ENV ?)
 *  - Possibilité de créer plusieurs instances, quand le SGBD est différent ? (Pour le SQLITE plus tard par exemple)
 *  - IL NE DOIT PLUS ÊTRE EXTENDS PAR LES MODELS !!!!!! (seulement l'ORM)
 */
class DatabaseManager
{
    protected static ?PDO $_instance = null;

    /**
     * @return \PDO
     * @esc This instance is the main instance, <b>please use this one instead of getLiteInstance()</b> for sql queries
     */
    public static function getInstance(): PDO
    {
        if (!is_null(self::$_instance)) {
            return self::$_instance;
        }

        try {
            $host = EnvManager::getInstance()->getValue('DB_HOST');
            $user = EnvManager::getInstance()->getValue('DB_USERNAME');
            $pass = EnvManager::getInstance()->getValue('DB_PASSWORD');

            self::$_instance = new PDO('mysql:host=' . $host . ';charset=utf8mb4', $user, $pass, [
                PDO::ATTR_PERSISTENT => true, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'
            ]);

            self::$_instance->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            self::$_instance->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            self::$_instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$_instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            /** Todo, see before if we have permissions ? */
            self::$_instance->exec('SET CHARACTER SET utf8mb4');
            self::$_instance->exec('CREATE DATABASE IF NOT EXISTS ' . EnvManager::getInstance()->getValue('DB_NAME') . ';');
            self::$_instance->exec('USE ' . EnvManager::getInstance()->getValue('DB_NAME') . ';');
            return self::$_instance;
        } catch (Exception $e) {
            die('DATABASE ERROR' . $e->getMessage());
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
        $dbServername = EnvManager::getInstance()->getValue('DB_HOST');
        $dbUsername = EnvManager::getInstance()->getValue('DB_USERNAME');
        $dbPassword = EnvManager::getInstance()->getValue('DB_PASSWORD');
        $dbName = EnvManager::getInstance()->getValue('DB_NAME');
        $dbPort = EnvManager::getInstance()->getValue('DB_PORT');

        $db = new PDO("mysql:host=$dbServername;port=$dbPort", $dbUsername, $dbPassword);
        $db->exec('SET CHARACTER SET utf8');
        $db->exec('CREATE DATABASE IF NOT EXISTS ' . $dbName . ';');
        $db->exec('USE ' . $dbName . ';');

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
        $db->exec('SET CHARACTER SET utf8');
        $db->exec('CREATE DATABASE IF NOT EXISTS ' . $name . ';');
        $db->exec('USE ' . $name . ';');

        return $db;
    }

    /**
     * @param string $file
     * @param array $options
     * @param bool $createMemoryDb
     * @return \PDO
     */
    public static function getCustomSqLiteInstance(string $file = 'db.sqlite3', array $options = [], bool $createMemoryDb = false): PDO
    {
        if ($createMemoryDb) {
            $db = new PDO('sqlite::memory:', $options);
        } else {
            $db = new PDO("sqlite:$file", $options);
        }

        return $db;
    }

    public static function isMariadb(): bool
    {
        $pdo = self::getInstance();
        $info = $pdo->query("SHOW VARIABLES like '%version%'")->fetchAll(PDO::FETCH_KEY_PAIR);

        return str_contains($info['version'], 'MariaDB');
    }
}

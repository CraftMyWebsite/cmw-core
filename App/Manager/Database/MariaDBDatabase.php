<?php

namespace CMW\Manager\Database;


use CMW\Manager\ORM\SGBD\Data\SGBDReceiver;
use PDO;
use PDOException;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\ORM\Database\getSGBDInstance;
use CMW\Manager\ORM\Database\SGBD;
class MariaDBDatabase implements SGBD
{

    use getSGBDInstance;

    private function setDatabaseAttributes(PDO $pdo): void {
        $pdo->setAttribute(PDO::ATTR_PERSISTENT, true);
        $pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8mb4");
        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    private function createDatabase(PDO $pdo): void {
        $pdo->exec("SET CHARACTER SET utf8mb4");
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . EnvManager::getInstance()->getValue("DB_NAME") . ";");
        $pdo->exec("USE " . EnvManager::getInstance()->getValue("DB_NAME") . ";");
    }

    public function connect(): PDO
    {
        try {
            $host = EnvManager::getInstance()->getValue("DB_HOST");
            $user = EnvManager::getInstance()->getValue("DB_USERNAME");
            $pass = EnvManager::getInstance()->getValue("DB_PASSWORD");

            $instance = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);

            $this->setDatabaseAttributes($instance);

            $this->createDatabase($instance);

            return $instance;
        } catch (PDOException $e) {
            die("DATABASE ERROR" . $e->getMessage()); //TODO see to implement error :D
        }
    }

    public function generate(SGBDReceiver $receiver): array
    {

        //TODO read receiver and generate query :D

        return array();
    }
}
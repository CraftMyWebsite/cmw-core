<?php

namespace CMW\Model\Installer;

use CMW\Manager\Download\DownloadManager;
use CMW\Utils\EnvManager;
use PDO;
use PDOException;

/**
 * Class: @installerModel
 * @package installer
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class InstallerModel
{

    private static function loadDatabase(string $serverName, string $database, string $username, $password, int $port): PDO
    {
        $db = new PDO("mysql:host=$serverName;port=$port", $username, $password);
        $db->exec("SET CHARACTER SET utf8");
        $db->exec("CREATE DATABASE IF NOT EXISTS " . $database . ";");
        $db->exec("USE " . $database . ";");

        return $db;
    }

    private static function loadDatabaseWithoutParams(): PDO
    {

        $dbServername = EnvManager::getInstance()->getValue("DB_HOST");
        $dbUsername = EnvManager::getInstance()->getValue("DB_USERNAME");
        $dbPassword = EnvManager::getInstance()->getValue("DB_PASSWORD");
        $dbName = EnvManager::getInstance()->getValue("DB_NAME");
        $dbPort = EnvManager::getInstance()->getValue("DB_PORT");

        return self::loadDatabase($dbServername, $dbName, $dbUsername, $dbPassword, $dbPort);
    }

    public static function tryDatabaseConnection(string $servername, string $username, string $password, int $port): bool
    {
        try {
            new PDO("mysql:host=$servername;port=$port", $username, $password);

            return true;
        } catch (PDOException) {
            return false;
        }
    }

    public static function initDatabase(string $serverName, string $database, string $username, string $password, int $port): void
    {
        $db = self::loadDatabase($serverName, $database, $username, $password, $port);

        $query = file_get_contents(EnvManager::getInstance()->getValue("dir") . "Installation/init.sql");
        $db->query($query);

        /* IMPORT PACKAGE SQL */
        self::loadDefaultPackages();
    }

    private static function loadDefaultPackages(): void
    {
        //Load packages files
        DownloadManager::initPackages('core', 'users', 'menus', 'pages');
    }

    public static function initAdmin(string $email, string $pseudo, string $password): void
    {

        $db = self::loadDatabaseWithoutParams();

        $query = $db->prepare('INSERT INTO cmw_users (user_email, user_pseudo, user_password, user_state, user_key, user_created, user_updated) VALUES (:user_email, :user_pseudo, :user_password, :user_state, :user_key, NOW(), NOW())');
        $query->execute(array(
            'user_email' => $email,
            'user_pseudo' => $pseudo,
            'user_password' => $password,
            'user_state' => 1,
            'user_key' => uniqid('', true)
        ));

        $query = $db->prepare("INSERT INTO cmw_users_roles (user_id, role_id) VALUES (:user_id, :role_id)");
        $query->execute(array(
            "user_id" => $db->lastInsertId(),
            "role_id" => 5 //Default administrator id is 5
        ));

    }

    public static function initConfig($name, $description): void
    {
        $db = self::loadDatabaseWithoutParams();

        $query = $db->prepare("INSERT INTO cmw_core_options (option_name, option_value, option_updated) VALUES (:option_name, :option_value, NOW())");

        $query->execute(array(
            "option_name" => "name",
            "option_value" => $name
        ));

        $query->execute(array(
            "option_name" => "description",
            "option_value" => $description
        ));
    }

}
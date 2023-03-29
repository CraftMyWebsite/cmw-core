<?php

use CMW\Utils\Utils;

/**
 * Class: @installerModel
 * @package installer
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class InstallerModel
{

    public function __construct()
    {
    }

    private static function loadDatabase($serverName, $database, $username, $password): PDO
    {
        $db = new PDO("mysql:host=$serverName", $username, $password);
        $db->exec("SET CHARACTER SET utf8");
        $db->exec("CREATE DATABASE IF NOT EXISTS " . $database . ";");
        $db->exec("USE " . $database . ";");

        return $db;
    }

    private static function loadDatabaseWithoutParams(): PDO
    {

        $dbServername = Utils::getEnv()->getValue("DB_HOST");
        $dbUsername = Utils::getEnv()->getValue("DB_USERNAME");
        $dbPassword = Utils::getEnv()->getValue("DB_PASSWORD");
        $dbName = Utils::getEnv()->getValue("DB_NAME");

        return self::loadDatabase($dbServername, $dbName, $dbUsername, $dbPassword);
    }

    public static function tryDatabaseConnection(string $servername, string $database, string $username, string $password): bool
    {
        try {
            new PDO("mysql:host=$servername", $username, $password);

            return true;
        } catch (PDOException $_) {
            return false;
        }
    }

    public static function initDatabase($serverName, $database, $username, $password, $devMode): void
    {
        $db = self::loadDatabase($serverName, $database, $username, $password);

        $query = file_get_contents(Utils::getEnv()->getValue("dir") . "installation/init.sql");
        $db->query($query);

        /* IMPORT PACKAGE SQL */
        self::loadPackages($db, $devMode);
    }

    private static function loadPackages(PDO $db, int $devMode): void
    {
        //Load sql files
        self::loadPackageSqlFiles($db, $devMode);
    }

    private static function loadPackageSqlFiles(PDO $db, int $devMode): void
    {
        $packageFolder = Utils::getEnv()->getValue("dir") . 'app/package/';
        $scannedDirectory = array_diff(scandir($packageFolder), array('..', '.'));

        foreach ($scannedDirectory as $package) {

            $sqlFolder = Utils::getEnv()->getValue("dir") . "app/package/$package/init";

            if (!is_dir($sqlFolder)) {
                continue;
            }

            $initFiles = array_diff(scandir($sqlFolder), array('..', '.'));

            if (empty($initFiles)) {
                continue;
            }

            foreach ($initFiles as $sqlFile) {

                $packageSqlFile = "$sqlFolder/$sqlFile";

                if (!is_file($packageSqlFile) || pathinfo($packageSqlFile, PATHINFO_EXTENSION) !== "sql") {
                    continue;
                }

                if (file_exists($packageSqlFile)) {
                    $querySqlFile = file_get_contents($packageSqlFile);
                    $stmtPackage = $db->query($querySqlFile);
                    $stmtPackage->closeCursor();
                    if ($devMode === 0) {
                        unlink($packageSqlFile);
                    }
                }


            }

        }
    }

    public static function initAdmin(string $email, string $username, string $password): void
    {

        $db = self::loadDatabaseWithoutParams();

        $query = $db->prepare('INSERT INTO cmw_users (user_email, user_pseudo, user_password, user_state, user_key, user_created, user_updated) VALUES (:user_email, :user_pseudo, :user_password, :user_state, :user_key, NOW(), NOW())');
        $query->execute(array(
            'user_email' => $email,
            'user_pseudo' => $username,
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
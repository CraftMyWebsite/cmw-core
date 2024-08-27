<?php

namespace CMW\Model\Installer;

use CMW\Controller\Core\PackageController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Download\DownloadManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\EncryptManager;
use CMW\Manager\Twofa\TwoFaManager;
use CMW\Model\Users\Users2FaModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
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

    public static function checkIfDatabaseAlreadyInstalled(string $servername, string $username, string $password, string $name, int $port): bool
    {
        $db = DatabaseManager::getCustomMysqlInstance($servername, $username, $password, $name, $port);

        $sql = "SHOW TABLES LIKE 'cmw_users'";

        $req = $db->query($sql);

        $res = $req->fetchAll();

        return count($res) >= 1;
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
        $packages = PackageController::getAllPackages();

        foreach ($packages as $package) {
            DownloadManager::initPackages($package->name());
        }
    }

    public static function initAdmin(string $email, string $pseudo, string $password): void
    {

        $db = self::loadDatabaseWithoutParams();

        $query = $db->prepare('INSERT INTO cmw_users (user_email, user_pseudo, user_password, user_state, user_key, user_created, user_updated) VALUES (:user_email, :user_pseudo, :user_password, :user_state, :user_key, NOW(), NOW())');
        $query->execute([
            'user_email' => $email,
            'user_pseudo' => $pseudo,
            'user_password' => $password,
            'user_state' => 1,
            'user_key' => uniqid('', true),
        ]);

        $userId = $db->lastInsertId();

        $query = $db->prepare("INSERT INTO cmw_users_roles (user_id, role_id) VALUES (:user_id, :role_id)");
        $query->execute([
            "user_id" => $userId,
            "role_id" => 5, //Default administrator id is 5
        ]);

        self::initCondition($userId);

        $tfaSecret = EncryptManager::encrypt((new TwoFaManager())->generateSecret());
        Users2FaModel::getInstance()->create($userId, $tfaSecret);

        $user = UsersModel::getInstance()->getUserById($userId);

        if (is_null($user)) {
            Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            Redirect::redirectPreviousRoute();
        }

        UsersController::getInstance()->loginUser($user, 1);
    }

    public static function initCondition(int $userId): void
    {
        $sql = "UPDATE cmw_core_condition SET condition_last_editor = :id";

        $db = self::loadDatabaseWithoutParams();

        $req = $db->prepare($sql);

        $req->execute(['id' => $userId]);
    }

    public static function initConfig($name, $description): void
    {
        $db = self::loadDatabaseWithoutParams();

        $query = $db->prepare("INSERT INTO cmw_core_options (option_name, option_value, option_updated) VALUES (:option_name, :option_value, NOW())");

        $query->execute([
            "option_name" => "name",
            "option_value" => $name,
        ]);

        $query->execute([
            "option_name" => "description",
            "option_value" => $description,
        ]);
    }

}
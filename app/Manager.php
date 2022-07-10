<?php
namespace CMW\Model;

use Exception;
use PDO;

/**
 * Class: @manager
 * @package Core
 * @author CraftMywebsite <contact@craftmywebsite.fr>
 * @version 1.0
 */
class Manager {
    protected static $db;

    /**
     * Return PDO Connexion
     *
     * @return \PDO
     */
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
            self::$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
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

    /**
     * Delete db line
     *
     *
     * @param string $tableName
     * @param string $columnName
     * @param int|string $id Id
     *
     * @return boolean
     */
    public static function dbDelete(string $tableName, string $columnName, int|string $id): bool{

        try {
            $sql = "DELETE FROM :tableName WHERE :columnName = :id";

            $db = self::dbConnect();
            $req = $db->prepare($sql);

            return $req->execute(array("tableName" => $tableName, "columnName" => $columnName, "id" => $id));

        }catch (\PDOException|Exception $exception){
            //TODO ERROR MANAGEMENT


            return false;
        }
    }

}
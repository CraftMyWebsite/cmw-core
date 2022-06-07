<?php

namespace INSTALLTION\GamesController;

use PDO;

class games {

    protected static $db;

    public static function dbConnect() : PDO {
        if(self::$db instanceof PDO) {
            return self::$db;
        }

        try
        {
            self::$db = new \PDO("mysql:host=".getenv("DB_HOST"),getenv("DB_USERNAME"),getenv("DB_PASSWORD"),
                array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', PDO::ATTR_PERSISTENT => true));
            self::$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$db->exec("SET CHARACTER SET utf8");
            self::$db->exec("CREATE DATABASE IF NOT EXISTS ".getenv("DB_NAME").";");
            self::$db->exec("USE ".getenv("DB_NAME").";");
            return self::$db;
        }
        catch (\Exception $e)
        {
            die(DATABASE_ERROR_MSG . $e->getMessage());
        }
    }

    public static array $availableGames = ["Minecraft", "Personal"];

    public static function installGame(string $gameName): void
    {

        switch ($gameName){
            case "Minecraft":
                (new games)->installMinecraft();
        }
    }

    public function installMinecraft(): void
    {
        $db = self::dbConnect();
        $query = file_get_contents("resources/gamesInit/Minecraft.sql");
        $db->exec($query);
    }


}

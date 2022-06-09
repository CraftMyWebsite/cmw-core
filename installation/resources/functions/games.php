<?php

namespace INSTALLTION\GamesController;

use CMW\Model\manager;
use Exception;
use PDO;

class games extends manager {

    protected static $db;

    public static array $availableGames = ["Minecraft", "Personal"];

    public static function installGame(string $gameName): void
    {

        switch ($gameName){
            case "Minecraft":
                (new self)->installMinecraft();
        }
    }

    public function installMinecraft(): void
    {
        $db = self::dbConnect();
        $query = file_get_contents("resources/gamesInit/Minecraft.sql");
        $db->exec($query);
    }


}

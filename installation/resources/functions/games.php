<?php

class games {

    public array $availableGames = ["Minecraft", "Personal"];

    public function installGame(string $gameName): void
    {

        switch ($gameName){
            case "Minecraft":

        }

    }

    public function installMinecraft(): void
    {
        $db = new PDO("mysql:host=".getenv('DB_HOST').";dbname=".getenv('DB_NAME')."", getenv('DB_USERNAME'), getenv('DB_PASSWORD'));


    }


}

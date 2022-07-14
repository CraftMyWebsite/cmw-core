<?php

namespace CMW\Controller\Installer\Games;

use CMW\Utils\Utils;

/**
 * Class: @FabricGames
 * @package games
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class FabricGames
{
    private static string $game;
    private const defaultGame = "personal";


    public function __construct()
    {
        self::$game = Utils::getEnv()->getValue("game") ?? self::defaultGame;
    }

    public static function getGame(): string
    {
        self::$game = Utils::getEnv()->getValue("game") ?? self::defaultGame;
        return self::$game;
    }

    public static function getGameList(): array
    {
        $toReturn = array();
        $path = Utils::getEnv()->getValue("dir") . "installation/tools/games/";
        $scannedDirectory = array_diff(scandir($path), array('..', '.'));

        foreach ($scannedDirectory as $file) {
            if (is_file("$path/$file")) {
                $toReturn[] = basename($file, '.php');
            }
        }

        return $toReturn;
    }

    public static function loadGames(): void
    {
        $path = Utils::getEnv()->getValue("dir") . "installation/tools/games/";
        $scannedDirectory = array_diff(scandir($path), array('..', '.'));
        foreach ($scannedDirectory as $file) {
            if (is_file("$path/$file")) {
                require_once("$path/$file");
            }
        }

        require_once("Games.php");
    }

    public static function installGame(string $string): void
    {

        self::loadGames();

        switch ($string) {
            case "minecraft":
                self::$game = "minecraft";
                Minecraft::install();
                break;
            default:
                self::$game = "personal";
                personal::install();
        }

    }

    public static function initConfig(): int
    {
        self::loadGames();

        return match (self::getGame()) {
            "minecraft" => Minecraft::initConfig(),
            default => personal::initConfig(),
        };
    }

    public static function getHTML(): void
    {

        self::loadGames();

        switch (self::getGame()) {
            case "minecraft":
                Minecraft::initConfigHTML();
                break;
            default:
                personal::initConfigHTML();
        }
    }

}
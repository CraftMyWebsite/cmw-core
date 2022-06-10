<?php

namespace CMW\Controller\Installer\Games;

/**
 * Class: @FabricGames
 * @package games
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class FabricGames
{
    private static string $game;


    public function __construct()
    {
        global $_UTILS;

        self::$game = $_UTILS::getEnv()->getValue("game") ?? "personal";

    }

    public static function getGame(): string
    {
        global $_UTILS;
        self::$game = $_UTILS::getEnv()->getValue("game") ?? "personal";
        return self::$game;
    }

    public static function getGameList(): array
    {
        global $_UTILS;

        $toReturn = array();
        $path = $_UTILS::getEnv()->getValue("dir") . "installation/tools/games/";
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
        global $_UTILS;

        $path = $_UTILS::getEnv()->getValue("dir") . "installation/tools/games/";
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
                minecraft::install();
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
            "minecraft" => minecraft::initConfig(),
            default => personal::initConfig(),
        };
    }

    public static function getHTML(): void
    {

        self::loadGames();

        switch (self::getGame()) {
            case "minecraft":
                minecraft::initConfigHTML();
                break;
            default:
                personal::initConfigHTML();
        }
    }

}
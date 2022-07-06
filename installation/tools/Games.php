<?php

namespace CMW\Controller\Installer\Games;

require_once("GameInterface.php");

/**
 * Class: @Games
 * @uses : @GameInterface
 * @package games
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
abstract class Games implements GameInterface
{
    private static string $gameName;

    public function __construct(string $name)
    {
        self::$gameName = $name;
    }

    public function setGameName($name): void
    {
        self::$gameName = $name;
    }

    public function getGameName($name): string
    {
        return self::$gameName;
    }

    public static function initConfig(): int
    {
        return 1;
    }

}
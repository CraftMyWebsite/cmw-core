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
    private string $gameName;

    public function __construct(string $name)
    {
        $this->gameName = $name;
    }

    public function setGameName($name): void
    {
        $this->gameName = $name;
    }

    public function getGameName($name): string
    {
        return $this->gameName;
    }

    public static function initConfig(): int {
        return 1;
    }

}
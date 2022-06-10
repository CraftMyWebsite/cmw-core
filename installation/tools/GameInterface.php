<?php

namespace CMW\Controller\Installer\Games;

/**
 * Interface: @GameIneterface
 * @package games
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
interface GameInterface
{
    public static function install(): void;
    public static function initConfig(): mixed;
    public static function initConfigHTML(): void;
}
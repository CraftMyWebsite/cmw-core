<?php

namespace CMW\Controller\Installer\Games;

use CMW\Model\manager;

require_once("installation/tools/Games.php");

/**
 * Class: @minecraft
 * @uses : @Games
 * @package games
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class minecraft extends Games
{

    private static function loadSql(): string
    {
        global $_UTILS;

        return file_get_contents($_UTILS::getEnv()->getValue("dir") . "installation/tools/init/minecraft.sql");
    }

    public static function install(): void
    {
        global $_UTILS;

        require_once($_UTILS::getEnv()->getValue("dir") . "app/manager.php");

        $db = manager::dbConnect();
        $query = self::loadSql();
        if ($query) {
            $db->exec($query);
        }
    }

    public static function initConfig(): int
    {
        global $_UTILS;

        if ($_UTILS::isValuesEmpty($_POST, "config_minecraft_ip")) {
            return -1;
        }

        $ip_minecraft = filter_input(INPUT_POST, "config_minecraft_ip");

        require_once($_UTILS::getEnv()->getValue("dir") . "app/manager.php");
        $db = manager::dbConnect();

        $query = $db->prepare("INSERT INTO cmw_core_options (option_name, option_value, option_updated) VALUES (:option_name, :option_value, NOW())");
        $query->execute(array(
            "option_name" => "minecraft_ip",
            "option_value" => $ip_minecraft
        ));

        return 1;
    }

    public static function initConfigHTML(): void
    {
        $msg = INSTALL_CONFIG_IP;
        echo <<< HTML
            <div class="form-group">
                <label for="config_minecraft_ip">$msg</label>
                <input type="text" name="config_minecraft_ip" class="form-control"
                           id="config_minecraft_ip" maxlength="255"
                           placeholder="mc.hypixel.net" required>
            </div>
        HTML;
    }
}
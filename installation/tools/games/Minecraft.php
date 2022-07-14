<?php

namespace CMW\Controller\Installer\Games;

use CMW\Model\Manager;
use CMW\Utils\Utils;

require_once("installation/tools/Games.php");

/**
 * Class: @minecraft
 * @uses : @Games
 * @package games
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class Minecraft extends Games
{

    public function __construct(string $name)
    {
        parent::__construct("minecraft");
    }


    private static function loadSql(): string
    {
        return file_get_contents(Utils::getEnv()->getValue("dir") . "installation/tools/init/minecraft.sql");
    }

    public static function install(): void
    {
        require_once(Utils::getEnv()->getValue("dir") . "app/manager.php");

        $db = Manager::dbConnect();
        $query = self::loadSql();
        if ($query) {
            $db->exec($query);
        }
    }

    public static function initConfig(): int
    {
        if (Utils::isValuesEmpty($_POST, "config_minecraft_ip")) {
            return -1;
        }

        $ip_minecraft = filter_input(INPUT_POST, "config_minecraft_ip");

        require_once(Utils::getEnv()->getValue("dir") . "app/manager.php");
        $db = Manager::dbConnect();

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
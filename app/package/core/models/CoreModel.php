<?php

namespace CMW\Model\Core;

use CMW\Manager\Database\DatabaseManager;

/**
 * Class: @coreController
 * @package Core
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class CoreModel extends DatabaseManager
{

    public function fetchOption($option): string
    {
        $db = self::getInstance();
        $req = $db->prepare('SELECT option_value FROM cmw_core_options WHERE option_name = ?');
        $req->execute(array($option));
        $option = $req->fetch();

        return $option['option_value'];
    }

    /***
     * @param $option
     * @return string
     * @desc get the selected option
     */
    public static function getOptionValue($option): string
    {
        $db = self::getInstance();
        $req = $db->prepare('SELECT option_value FROM cmw_core_options WHERE option_name = ?');
        $req->execute(array($option));

        return ($req->execute()) ? $req->fetch()["option_value"] : "";
    }

    public function fetchOptions(): array
    {
        $db = $this->getInstance();
        $req = $db->prepare('SELECT * FROM cmw_core_options');

        if ($req->execute()) {
            return $req->fetchAll();
        }

        return ($req->execute()) ? $req->fetchAll() : [];
    }

    public static function updateOption(string $option_name, string $option_value): void
    {
        $db = self::getInstance();
        $req = $db->prepare('UPDATE cmw_core_options SET option_value=:option_value, option_updated=now() WHERE option_name=:option_name');
        $req->execute(array("option_name" => $option_name, "option_value" => $option_value));
    }

    public static function getLanguages(string $prefix): array|string
    {
        foreach (get_defined_constants(false) as $key => $value) {
            if (str_starts_with($key, mb_strtoupper($prefix, 'UTF-8'))) {
                $dump[$key] = $value;
            }
        }

        //Todo Error Manager.
        return !empty($dump) ? $dump : "Error: No Constants found with prefix '" . $prefix . "'";
    }
}
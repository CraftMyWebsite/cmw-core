<?php

namespace CMW\Model\Core;

use CMW\Manager\Cache\SimpleCacheManager;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;

/**
 * Class: @coreController
 * @package Core
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class CoreModel extends AbstractModel
{

    public function fetchOption(string $option): string
    {
//        TODO Le cache ne fonctionne pas et du coup ralenti le chargement des page
        /*if (SimpleCacheManager::cacheExist('options', "Options")){
            $data = SimpleCacheManager::getCache('options', "Options");

            foreach ($data as $conf) {
                if ($conf['option_name'] === $option){
                    return $conf['option_value'] ?? "UNDEFINED_$option";
                }
            }
        }*/

        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT option_value FROM cmw_core_options WHERE option_name = ?');
        $req->execute(array($option));
        $option = $req->fetch();

        return $option['option_value'];
    }

    /**
     * @param string $option
     * @return string
     * @desc get the selected option
     */
    public static function getOptionValue(string $option): string
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT option_value FROM cmw_core_options WHERE option_name = ?');

        return ($req->execute(array($option))) ? $req->fetch()["option_value"] : "";
    }

    public function fetchOptions(): array
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT * FROM cmw_core_options');

        if ($req->execute()) {
            return $req->fetchAll();
        }

        return ($req->execute()) ? $req->fetchAll() : [];
    }

    public function updateOption(string $option_name, string $option_value): bool
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('UPDATE cmw_core_options SET option_value=:option_value, option_updated=NOW() WHERE option_name=:option_name');
        return $req->execute(array("option_name" => $option_name, "option_value" => $option_value));
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
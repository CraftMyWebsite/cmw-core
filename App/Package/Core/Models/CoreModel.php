<?php

namespace CMW\Model\Core;

use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use function get_defined_constants;
use function mb_strtoupper;
use function str_starts_with;

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
        $req->execute([$option]);
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

        return ($req->execute([$option])) ? $req->fetch()['option_value'] : '';
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

    /**
     * @param string $option_name
     * @param string $option_value
     * @return bool
     */
    public function updateOption(string $option_name, string $option_value): bool
    {
        $sql = 'INSERT INTO cmw_core_options (option_name, option_value, option_updated) 
                VALUES (:option_name, :option_value, NOW()) 
                ON DUPLICATE KEY UPDATE option_value=VALUES(option_value), option_updated=NOW()';
        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute(['option_name' => $option_name, 'option_value' => $option_value]);
    }
}

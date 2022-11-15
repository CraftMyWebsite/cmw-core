<?php

namespace CMW\Model\Users;


use CMW\Manager\Database\DatabaseManager;


/**
 * Class: @UsersSettingsModel
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class UsersSettingsModel extends DatabaseManager
{
    public static function getSetting(string $settingName): string
    {
        $db = self::getInstance();
        $req = $db->prepare('SELECT users_settings_value FROM cmw_users_settings WHERE users_settings_name = ?');
        $req->execute(array($settingName));
        $option = $req->fetch();

        return $option['users_settings_value'];
    }

    public function getSettings(): array
    {
        $db = self::getInstance();
        $req = $db->prepare('SELECT * FROM cmw_users_settings');

        if ($req->execute()) {
            return $req->fetchAll();
        }

        return ($req->execute()) ? $req->fetchAll() : [];
    }

    public static function updateSetting(string $settingName, string $settingValue): void
    {
        $db = self::getInstance();
        $req = $db->prepare('UPDATE cmw_users_settings SET users_settings_value=:settingValue, users_settings_updated=now() WHERE users_settings_name=:settingName');
        $req->execute(array("settingName" => $settingName, "settingValue" => $settingValue));
    }

    public static function addSetting(string $settingName, string $settingValue): void
    {
        $db = self::getInstance();
        $req = $db->prepare('INSERT INTO cmw_users_settings (users_settings_value, users_settings_updated, users_settings_name) 
                                    VALUES (:settingValue, now(), :settingName)');
        $req->execute(array("settingName" => $settingName, "settingValue" => $settingValue));
    }

    public static function deleteSetting(string $settingName): void
    {
        $db = self::getInstance();
        $req = $db->prepare('DELETE FROM cmw_users_settings where users_settings_name = :settingName');
        $req->execute(array("settingName" => $settingName));
    }
}
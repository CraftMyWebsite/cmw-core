<?php

namespace CMW\Model\Users;


use CMW\Entity\Users\BlacklistedPseudoEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;


/**
 * Class: @UsersSettingsModel
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class UsersSettingsModel extends AbstractModel
{
    public static function getSetting(string $settingName): string
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT users_settings_value FROM cmw_users_settings WHERE users_settings_name = ?');
        $req->execute(array($settingName));
        $option = $req->fetch();

        return $option['users_settings_value'];
    }

    public function getSettings(): array
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT * FROM cmw_users_settings');

        if ($req->execute()) {
            return $req->fetchAll();
        }

        return ($req->execute()) ? $req->fetchAll() : [];
    }

    public static function updateSetting(string $settingName, string $settingValue): void
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('UPDATE cmw_users_settings SET users_settings_value=:settingValue, users_settings_updated=now() WHERE users_settings_name=:settingName');
        $req->execute(array("settingName" => $settingName, "settingValue" => $settingValue));
    }

    public static function addSetting(string $settingName, string $settingValue): void
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('INSERT INTO cmw_users_settings (users_settings_value, users_settings_updated, users_settings_name) 
                                    VALUES (:settingValue, now(), :settingName)');
        $req->execute(array("settingName" => $settingName, "settingValue" => $settingValue));
    }

    public static function deleteSetting(string $settingName): void
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('DELETE FROM cmw_users_settings where users_settings_name = :settingName');
        $req->execute(array("settingName" => $settingName));
    }

    /**
     * @param string $pseudo
     * @return bool
     */
    public function addBlacklistedPseudo(string $pseudo): bool
    {
        $sql = "INSERT INTO cmw_users_blacklist_pseudo (pseudo) 
                    VALUES (:pseudo)";
        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute(['pseudo' => $pseudo]);
    }

    /**
     * @param int $id
     * @param string $pseudo
     * @return bool
     */
    public function editBlacklistedPseudo(int $id, string $pseudo): bool
    {
        $sql = "UPDATE cmw_users_blacklist_pseudo SET pseudo = :pseudo WHERE id = :id";
        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute(['id' => $id, 'pseudo' => $pseudo]);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function removeBlacklistedPseudo(int $id): bool
    {
        $sql = "DELETE FROM cmw_users_blacklist_pseudo WHERE id = :id";
        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute(['id' => $id]);
    }

    /**
     * @return \CMW\Entity\Users\BlacklistedPseudoEntity[]
     */
    public function getBlacklistedPseudos(): array
    {
        $sql = "SELECT * FROM cmw_users_blacklist_pseudo";
        $db = DatabaseManager::getInstance();
        $req = $db->query($sql);

        $res = $req->fetchAll();

        if (!$res){
            return [];
        }

        $toReturn = [];

        foreach ($res as $pseudo) {
            $toReturn[] = new BlacklistedPseudoEntity(
                $pseudo['id'],
                $pseudo['pseudo'],
                $pseudo['blacklisted_at']
            );
        }

        return $toReturn;
    }

    /**
     * @param int $id
     * @return \CMW\Entity\Users\BlacklistedPseudoEntity|null
     */
    public function getBlacklistedPseudo(int $id): ?BlacklistedPseudoEntity
    {
        $sql = "SELECT * FROM cmw_users_blacklist_pseudo WHERE id = :id";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(['id' => $id])){
            return null;
        }

        $res = $req->fetch();

        if (!$res){
            return null;
        }

        return new BlacklistedPseudoEntity(
            $res['id'],
            $res['pseudo'],
            $res['blacklisted_at']
        );
    }

    /**
     * @param string $pseudo
     * @return bool
     */
    public function isPseudoBlacklisted(string $pseudo): bool
    {
        $sql = "SELECT id FROM cmw_users_blacklist_pseudo WHERE pseudo = :pseudo";
        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if(!$req->execute(['pseudo' => $pseudo])){
            return true;
        }

        $res = $req->fetch();

        if (!$res){
            return false;
        }

        return count($res) >= 1;
    }
}
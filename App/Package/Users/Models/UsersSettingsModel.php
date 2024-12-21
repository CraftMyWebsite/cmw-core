<?php

namespace CMW\Model\Users;

use CMW\Entity\Users\BlacklistedPseudoEntity;
use CMW\Entity\Users\Settings\BulkSettingsEntity;
use CMW\Entity\Users\UserEnforced2FaEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractModel;
use Exception;
use RuntimeException;

/**
 * Class: @UsersSettingsModel
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class UsersSettingsModel extends AbstractModel
{
    public function getSetting(string $settingName): string
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT users_settings_value FROM cmw_users_settings WHERE users_settings_name = ?');
        $req->execute([$settingName]);
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

    public function updateSetting(string $settingName, string $settingValue): void
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('UPDATE cmw_users_settings SET users_settings_value=:settingValue, users_settings_updated=now() WHERE users_settings_name=:settingName');
        $req->execute(['settingName' => $settingName, 'settingValue' => $settingValue]);
    }

    /**
     * @param BulkSettingsEntity ...$bulkSettings
     * @return bool
     */
    public function bulkUpdateSettings(BulkSettingsEntity ...$bulkSettings): bool
    {
        $db = DatabaseManager::getInstance();

        $db->beginTransaction();

        try {
            $stmt = $db->prepare('UPDATE cmw_users_settings SET users_settings_value = :value WHERE users_settings_name = :name');

            foreach ($bulkSettings as $bulkSetting) {
                $data = ['name' => $bulkSetting->getName(), 'value' => $bulkSetting->getValue()];

                if (!$stmt->execute($data)) {
                    throw new RuntimeException('Failed to execute statement');
                }
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    public function addSetting(string $settingName, string $settingValue): void
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('INSERT INTO cmw_users_settings (users_settings_value, users_settings_updated, users_settings_name) 
                                    VALUES (:settingValue, now(), :settingName)');
        $req->execute(['settingName' => $settingName, 'settingValue' => $settingValue]);
    }

    public function bulkAddSettings(BulkSettingsEntity ...$bulkSettings): bool
    {
        $db = DatabaseManager::getInstance();

        $sql = "INSERT INTO cmw_users_settings (users_settings_name, users_settings_value) VALUES ";

        $values = [];
        $data = [];
        foreach ($bulkSettings as $bulkSetting) {
            $values[] = "(:name{$bulkSetting->getName()}, :value{$bulkSetting->getValue()})";

            $data[] = [
                'name' . $bulkSetting->getName() => $bulkSetting->getName(),
                'value' . $bulkSetting->getValue() => $bulkSetting->getValue(),
            ];
        }

        $sql .= implode(', ', $values);

        return $db->prepare($sql)->execute($data);
    }

    public function deleteSetting(string $settingName): void
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('DELETE FROM cmw_users_settings where users_settings_name = :settingName');
        $req->execute(['settingName' => $settingName]);
    }

    /**
     * @param string $pseudo
     * @return bool
     */
    public function addBlacklistedPseudo(string $pseudo): bool
    {
        $sql = 'INSERT INTO cmw_users_blacklist_pseudo (pseudo) 
                    VALUES (:pseudo)';
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
        $sql = 'UPDATE cmw_users_blacklist_pseudo SET pseudo = :pseudo WHERE id = :id';
        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute(['id' => $id, 'pseudo' => $pseudo]);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function removeBlacklistedPseudo(int $id): bool
    {
        $sql = 'DELETE FROM cmw_users_blacklist_pseudo WHERE id = :id';
        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute(['id' => $id]);
    }

    /**
     * @return BlacklistedPseudoEntity[]
     */
    public function getBlacklistedPseudos(): array
    {
        $sql = 'SELECT * FROM cmw_users_blacklist_pseudo';
        $db = DatabaseManager::getInstance();
        $req = $db->query($sql);

        $res = $req->fetchAll();

        if (!$res) {
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
     * @return BlacklistedPseudoEntity|null
     */
    public function getBlacklistedPseudo(int $id): ?BlacklistedPseudoEntity
    {
        $sql = 'SELECT * FROM cmw_users_blacklist_pseudo WHERE id = :id';
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(['id' => $id])) {
            return null;
        }

        $res = $req->fetch();

        if (!$res) {
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
        $sql = 'SELECT id FROM cmw_users_blacklist_pseudo WHERE pseudo = :pseudo';
        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(['pseudo' => $pseudo])) {
            return true;
        }

        $res = $req->fetch();

        if (!$res) {
            return false;
        }

        return count($res) >= 1;
    }

    /**
     * @return UserEnforced2FaEntity[]
     */
    public function getEnforcedRoles(): array
    {
        $sql = 'SELECT * FROM cmw_users_enforced2fa_roles';
        $db = DatabaseManager::getInstance();
        $req = $db->query($sql);

        $res = $req->fetchAll();

        if (!$res) {
            return [];
        }

        $toReturn = [];

        foreach ($res as $enforcedRole) {
            $toReturn[] = new UserEnforced2FaEntity(
                RolesModel::getInstance()->getRoleById($enforcedRole['enforced2fa_roles'])
            );
        }
        return $toReturn;
    }

    public function updateEnforcedRoles($roleId): bool
    {
        foreach (RolesModel::getInstance()->getRoles() as $role) {
            if ($role->getId() === $roleId) {
                if ($this->addEnforcedRoles($roleId)) {
                    foreach (UsersModel::getInstance()->getUsers() as $user) {
                        foreach ($user->getRoles() as $userRole) {
                            if ($userRole->getId() === $role->getId()) {
                                Users2FaModel::getInstance()->enforce2Fa($user->getId());
                            }
                        }
                    }
                } else {
                    Flash::send(Alert::ERROR, 'Erreur', "Impossible d'ajouter les rôles à l'imposition du 2fa");
                    return false;
                }
            }
        }
        return true;
    }

    public function clearEnforcedRoles(): bool
    {
        Users2FaModel::getInstance()->clearEnforce2Fa();

        $sql = 'DELETE FROM cmw_users_enforced2fa_roles';

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute();
    }

    private function addEnforcedRoles($roleId): bool
    {
        $sql = 'INSERT INTO cmw_users_enforced2fa_roles (enforced2fa_roles) 
                VALUES (:enforced2fa_roles)';
        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute(['enforced2fa_roles' => $roleId]);
    }
}

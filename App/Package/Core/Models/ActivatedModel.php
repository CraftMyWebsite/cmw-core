<?php

namespace CMW\Model\Core;

use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;

/**
 * Class: @ActivatedModel
 * @package Core
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/models
 */
class ActivatedModel extends AbstractModel
{
    /**
     * @return array
     */
    public function getActivations(): array
    {
        $sql = "SELECT * FROM cmw_actived_resources";
        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute()) {
            return [];
        }

        $req = $req->fetchAll();

        if (!empty($req)) {
            return $req;
        }

        return [];
    }

    /**
     * @param int $resId
     * @return ?array
     */
    public function getActivationByResId(int $resId): ?array
    {
        $sql = "SELECT * FROM cmw_actived_resources WHERE resource_id = :resource_id ORDER BY activated_resource_id DESC LIMIT 1";
        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(['resource_id' => $resId])) {
            return null;
        }

        $row = $req->fetch();

        return $row ?: null;
    }

    /**
     * @param string $activationKey
     * @param int $ressourceId
     * @param string $resName
     * @return false|int
     * @desc Add Key for user to res
     */
    public function addActivation(string $activationKey, int $ressourceId, string $resName): false|int
    {
        $var = [
            'resource_key' => $activationKey,
            'resource_id' => $ressourceId,
            'resource_name' => $resName,
        ];

        $sql = "INSERT INTO cmw_actived_resources (resource_key, resource_id, resource_name) VALUES (:resource_key, :resource_id, :resource_name)";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute($var)) {
            return false;
        }

        return $db->lastInsertId();
    }

    /**
     * @param string $resName
     * @return void
     */
    public function removeActivationByResName(string $resName): void
    {
        $sql = 'DELETE FROM cmw_actived_resources WHERE resource_name = :name';
        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);
        $req->execute(['name' => $resName]);
    }
}

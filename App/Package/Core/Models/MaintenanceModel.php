<?php

namespace CMW\Model\Core;

use CMW\Entity\Core\MaintenanceEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;

/**
 * Class: @MaintenanceModel
 * @package Core
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class MaintenanceModel extends AbstractModel
{

    /**
     * @return \CMW\Entity\Core\MaintenanceEntity
     */
    public function getMaintenance(): MaintenanceEntity
    {
        $sql = "SELECT * FROM cmw_maintenance LIMIT 1";
        $db = DatabaseManager::getInstance();

        $req = $db->query($sql);

        $res = $req->fetch();

        return new MaintenanceEntity(
            $res['maintenance_is_enable'],
            $res['maintenance_title'],
            $res['maintenance_description'],
            $res['maintenance_type'],
            $res['maintenance_target_date'],
            $res['maintenance_last_updated_at'],
            $res['maintenance_is_override_theme'],
            $res['maintenance_override_theme_code']
        );
    }

    /**
     * @param int $isEnable
     * @param string $title
     * @param string $description
     * @param int $type
     * @param string $targetDate
     * @param int $isOverrideTheme
     * @param string $overrideThemeCode
     * @return bool
     */
    public function updateMaintenance(int $isEnable, string $title, string $description, int $type, string $targetDate, int $isOverrideTheme, string $overrideThemeCode): bool
    {
        $var = [
            'isEnable' => $isEnable,
            'title' => $title,
            'description' => $description,
            'type' => $type,
            'targetDate' => $targetDate,
            'isOverrideTheme' => $isOverrideTheme,
            'overrideThemeCode' => $overrideThemeCode
        ];

        $sql = "UPDATE cmw_maintenance SET maintenance_is_enable = :isEnable, maintenance_title = :title, 
                           maintenance_description = :description, maintenance_type = :type, 
                           maintenance_target_date = :targetDate, maintenance_is_override_theme = :isOverrideTheme,
                           maintenance_override_theme_code = :overrideThemeCode";
        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute($var);
    }


}
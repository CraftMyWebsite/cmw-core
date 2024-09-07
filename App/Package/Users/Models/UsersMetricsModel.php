<?php

namespace CMW\Model\Users;

use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractModel;

/**
 * Class: @UsersMetricsModel
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class UsersMetricsModel extends AbstractModel
{
    /**
     * @param int $pastMonths
     * @return array
     */
    public function getPastMonthsRegisterNumbers(int $pastMonths): array
    {
        $toReturn = [];

        for ($i = 0; $i < $pastMonths; $i++) {
            $targetMonth = idate('m', strtotime("-$i months"));
            $targetMonthTranslate = LangManager::translate("core.months.$targetMonth");

            $rangeStart = date('Y-m-d 00:00:00', strtotime("first day of -$i months"));
            $rangeFinish = date('Y-m-d 23:59:59', strtotime("last day of -$i months"));

            $toReturn[$targetMonthTranslate] = $this->getDataRegisters($rangeStart, $rangeFinish);
        }
        return array_reverse($toReturn);
    }

    /**
     * @param string $rangeStart
     * @param string $rangeFinish
     * @return int
     */
    public function getDataRegisters(string $rangeStart, string $rangeFinish): int
    {
        $var = array(
            'range_start' => $rangeStart,
            'range_finish' => $rangeFinish
        );

        $sql = 'SELECT COUNT(user_id) AS `result` FROM cmw_users WHERE user_created BETWEEN (:range_start) AND (:range_finish)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $res = $req->execute($var);

        if (!$res) {
            return 0;
        }

        return $req->fetch()['result'] ?? 0;
    }

    /**
     * @param string $rangeStart
     * @param string $rangeFinish
     * @return int
     */
    public function getDataLogins(string $rangeStart, string $rangeFinish): int
    {
        $var = array(
            'range_start' => $rangeStart,
            'range_finish' => $rangeFinish
        );

        $sql = 'SELECT COUNT(user_id) AS `result` FROM cmw_users WHERE user_logged BETWEEN (:range_start) AND (:range_finish)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $res = $req->execute($var);

        if (!$res) {
            return 0;
        }

        return $req->fetch()['result'] ?? 0;
    }
}

<?php

namespace CMW\Utils;

use CMW\Manager\Lang\LangManager;
use function array_reverse;
use function date;
use function idate;
use function strtotime;

class Date
{
    /**
     * @param int $seconds
     * @return string
     * @desc Convert seconds to time format.
     */
    public static function secondsToTime(int $seconds): string
    {
        $secs = $seconds % 60;
        $hrs = $seconds / 60;
        $mins = $hrs % 60;

        $hrs /= 60;

        return (int)$hrs . "h " . $mins . "m " . $secs . "s";
    }

    /**
     * @param int $pastMonths
     * @return array
     * @desc Get past months from now to - past months.
     */
    public static function getPastMonths(int $pastMonths): array
    {
        $toReturn = [];

        for ($i = 0; $i < $pastMonths; $i++) {
            $targetMonth = idate('m', strtotime("-$i months"));
            $toReturn[] = LangManager::translate("core.months.$targetMonth");
        }

        return array_reverse($toReturn);
    }

    /**
     * @param int $pastWeeks
     * @return array
     * @desc Get past weeks from now to - past weeks.
     */
    public static function getPastWeeks(int $pastWeeks): array
    {
        $toReturn = [];

        for ($i = 0; $i < $pastWeeks; $i++) {
            $targetWeek = date('W', strtotime("-$i weeks"));
            $toReturn[] = $targetWeek;
        }

        return array_reverse($toReturn);
    }

    /**
     * @param int $pastDays
     * @return array
     * @desc Get past days from now to - past days.
     */
    public static function getPastDays(int $pastDays): array
    {
        $toReturn = [];

        for ($i = 0; $i < $pastDays; $i++) {
            $toReturn[] = date('d/m', strtotime("-$i days"));
        }

        return array_reverse($toReturn);
    }
}
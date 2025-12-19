<?php

namespace CMW\Utils;

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Core\CoreModel;
use function array_reverse;
use function date;
use function idate;
use function strtotime;

class Date
{
    public static array $exampleDateFormat = ['d-m-Y H:i:s', 'd-m-Y Hh im ss', 'd/m/Y H:i:s', 'd/m/Y à H\h i\m s\s', 'd/m/Y à H\h i\m', 'd/m/Y at H\h i\m s\s', 'd F \à H\hi', 'd F \at H\hi'];

    /**
     * @param string $date
     * @return string
     * @desc Convert DateTime into formatted Date respecting locale.
     */
    public static function formatDate(string $date): string
    {
        $formatted = date(CoreModel::getInstance()->fetchOption('dateFormat'), strtotime($date));

        switch (EnvManager::getInstance()->getValue('LOCALE')) {
            case 'fr':
                $months = [
                    'January' => 'janvier', 'February' => 'février', 'March' => 'mars',
                    'April' => 'avril', 'May' => 'mai', 'June' => 'juin',
                    'July' => 'juillet', 'August' => 'août', 'September' => 'septembre',
                    'October' => 'octobre', 'November' => 'novembre', 'December' => 'décembre'
                ];
                break;

            case 'zh': // Chinois simplifié
                $months = [
                    'January' => '一月', 'February' => '二月', 'March' => '三月',
                    'April' => '四月', 'May' => '五月', 'June' => '六月',
                    'July' => '七月', 'August' => '八月', 'September' => '九月',
                    'October' => '十月', 'November' => '十一月', 'December' => '十二月'
                ];
                break;

            case 'ja': // Japonais
                $months = [
                    'January' => '1月', 'February' => '2月', 'March' => '3月',
                    'April' => '4月', 'May' => '5月', 'June' => '6月',
                    'July' => '7月', 'August' => '8月', 'September' => '9月',
                    'October' => '10月', 'November' => '11月', 'December' => '12月'
                ];
                break;

            case 'es': // Espagnol
                $months = [
                    'January' => 'enero', 'February' => 'febrero', 'March' => 'marzo',
                    'April' => 'abril', 'May' => 'mayo', 'June' => 'junio',
                    'July' => 'julio', 'August' => 'agosto', 'September' => 'septiembre',
                    'October' => 'octubre', 'November' => 'noviembre', 'December' => 'diciembre'
                ];
                break;

            case 'de': // Allemand
                $months = [
                    'January' => 'Januar', 'February' => 'Februar', 'March' => 'März',
                    'April' => 'April', 'May' => 'Mai', 'June' => 'Juni',
                    'July' => 'Juli', 'August' => 'August', 'September' => 'September',
                    'October' => 'Oktober', 'November' => 'November', 'December' => 'Dezember'
                ];
                break;

            default:
                return $formatted;
        }

        return strtr($formatted, $months);
    }

    /**
     * @param int $date
     * @return string
     * @deprecated Please use timestampToTime()
     */
    public static function formatDateTime(int $date): string
    {
        return date(CoreModel::getInstance()->fetchOption('dateFormat'), $date);
    }

    /**
     * @param int $date
     * @return string
     */
    public static function timestampToTime(int $date): string
    {
        return date(CoreModel::getInstance()->fetchOption('dateFormat'), $date);
    }

    /**
     * @param string $date
     * @return string
     */
    public static function dateToTimestamp(string $date): string
    {
        return (string)strtotime($date);
    }

    /**
     * @return int
     */
    public static function getCurrentTimestamp(): int
    {
        return time();
    }

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
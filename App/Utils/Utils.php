<?php

namespace CMW\Utils;

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use ReflectionClass;

/**
 * Class: @Utils
 * @package Utils
 * @author CraftMywebsite <contact@craftmywebsite.fr>
 * @version 1.0
 */
class Utils
{

    public static function isValuesEmpty(array $array, string ...$values): bool
    {
        foreach ($values as $value) {
            if (empty($array[$value])) {
                return true;
            }
        }

        return false;
    }

    public static function containsNullValue(?string ...$values): bool
    {
        foreach ($values as $value) {
            if (is_null($value)) {
                return true;
            }
        }

        return false;
    }

    public static function normalizeForSlug($text, $encode = "UTF-8"): string
    {
        $text = mb_strtolower(trim(self::removeAccents($text, $encode)));
        $text = preg_replace("/\s+/", "-", $text);
        $text = preg_replace("/(-)\\1+/", "$1", $text);
        $text = preg_replace("/[^A-z\-\d]/", "", $text);
        if ($text[strlen($text) - 1] === '-') {
            $text = substr_replace($text, "", -1);
        }
        return $text;
    }

    public static function removeAccents($text, $encode = "UTF-8"): string
    {
        $text = preg_replace("/['\"^]/", "-", $text);
        return preg_replace("~&([A-z]{1,2})(acute|cedil|caron|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i", "$1", htmlentities($text, ENT_QUOTES, $encode));
    }

    public static function addIfNotNull(array &$array, mixed $value): void
    {
        if (!is_null($value)) {
            $array[] = $value;
        }
    }

    public static function filterInput(string ...$values): array
    {
        $toReturn = array();
        foreach ($values as $value) {
            $toReturn[] = filter_input(INPUT_POST, $value);
        }

        return $toReturn;
    }

    /**
     * @param $object
     * @return array
     */
    public static function objectToArray($object): array
    {
        $reflectionClass = new ReflectionClass(get_class($object));
        $array = array();
        foreach ($reflectionClass->getProperties() as $property) {
            $array[$property->getName()] = $property->getValue($object);
        }
        return $array;
    }

    /**
     * @param int $l
     * @return string
     * @desc Return a string ID
     */
    public static function genId(int $l = 5): string
    {
        return substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 10, $l);
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
            $targetMonth = idate("m", strtotime("-$i months"));
            $toReturn[] = LangManager::translate("core.months.$targetMonth");
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
}

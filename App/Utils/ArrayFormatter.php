<?php

namespace CMW\Utils;

use ReflectionClass;
use function array_keys;
use function get_class;
use function shuffle;

class ArrayFormatter
{
    /**
     * @param array $array
     * @param int $numberValues
     * @return array[]
     */
    private static function shuffleArray(array &$array, int $numberValues): array
    {
        $keys = array_keys($array);

        shuffle($keys);

        $toReturn = [];

        $index = 0;
        foreach ($keys as $key) {
            if ($index === $numberValues) {
                return [...$toReturn];
            }

            $toReturn[$key] = $array[$key];
            ++$index;
        }

        $array = $toReturn;

        return [...$toReturn];
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
}
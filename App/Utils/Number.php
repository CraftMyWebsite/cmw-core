<?php

namespace CMW\Utils;

use function ceil;
use function round;

class Number
{
    /**
     * <p>Rounds a number up to the nearest thousand.</p>
     * @param int $number
     * @return int
     */
    public static function roundUpToThousand(int $number): int
    {
        if ($number % 1000 === 0) {
            return $number;
        }
        return ceil($number / 1000) * 1000;
    }

    /**
     * <p>Rounds a number down to the nearest thousand.</p>
     * @param int $number
     * @return int
     */
    public static function roundDownToThousand(int $number): int
    {
        if ($number % 1000 === 0) {
            return $number;
        }
        return floor($number / 1000) * 1000;
    }

    /**
     * <p>Converts an integer to its Roman numeral representation.</p>
     * @param int $number
     * @return string
     */
    public static function toRoman(int $number): string
    {
        $map = [
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1,
        ];
        $result = '';
        foreach ($map as $roman => $value) {
            while ($number >= $value) {
                $result .= $roman;
                $number -= $value;
            }
        }
        return $result;
    }

    /**
     * <p>Formats a number into a compact string representation using suffixes like K, M, B, T.</p>
     * @param int $number
     * @return string
     */
    public static function toCompact(int $number): string
    {
        if ($number >= 1_000_000_000_000) {
            return round($number / 1_000_000_000_000, 1) . 'T';
        }

        if ($number >= 1_000_000_000) {
            return round($number / 1_000_000_000, 1) . 'B';
        }

        if ($number >= 1_000_000) {
            return round($number / 1_000_000, 1) . 'M';
        }

        if ($number >= 1_000) {
            return round($number / 1_000, 1) . 'K';
        }

        return (string)$number;
    }
}
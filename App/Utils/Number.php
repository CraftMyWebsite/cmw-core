<?php

namespace CMW\Utils;

use function ceil;

class Number
{
    public static function roundUpToThousand(int $number): int {
        if ($number % 1000 === 0) {
            return $number;
        }
        return ceil($number / 1000) * 1000;
    }
}
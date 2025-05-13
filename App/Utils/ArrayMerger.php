<?php

namespace CMW\Utils;

class ArrayMerger
{
    public const int MERGE_OVERWRITE = 0;
    public const int MERGE_APPEND = 1;
    public const int MERGE_REPLACE = 2;

    /**
     * Merges arrays recursively
     *
     * If last argument is an integer, it defines the
     * behavior for elements with numeric keys;
     * - A::MERGE_OVERWRITE:  elements are overwritten, keys are preserved
     * - A::MERGE_APPEND:     elements are appended, keys are reset;
     * - A::MERGE_REPLACE:    non-associative arrays are completely replaced
     * @param array|int ...$arrays
     * @return array
     *
     * This method is originally from the kirby CMS. Link: https://github.com/getkirby/kirby/blob/main/src/Toolkit/A.php#L487
     *
     */
    public static function merge(array|int ...$arrays): array
    {
        $last = array_pop($arrays);
        $mode = is_int($last) ? array_pop($arrays) : A::MERGE_APPEND;

        $merged = array_shift($arrays);
        $join = array_shift($arrays);

        if ($mode === static::MERGE_REPLACE && Arr::isAssociative($merged) === false) {
            $merged = $join;
        } else {
            foreach ($join as $key => $value) {
                if (is_int($key) === true && $mode === static::MERGE_APPEND) {
                    $merged[] = $value;

                } elseif (is_array($value) === true && isset($merged[$key]) === true && is_array($merged[$key]) === true) {
                    $merged[$key] = static::merge($merged[$key], $value, $mode);
                } else {
                    $merged[$key] = $value;
                }
            }

            if ($mode === static::MERGE_APPEND) {
                $merged = array_merge($merged, []);
            }
        }

        if (count($arrays) > 0) {
            array_unshift($arrays, $merged);
            $arrays[] = $mode;
            return static::merge(...$arrays);
        }

        return $merged;
    }
}
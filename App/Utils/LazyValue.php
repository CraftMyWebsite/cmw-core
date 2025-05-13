<?php

namespace CMW\Utils;

use Closure;

class LazyValue
{
    public function __construct(protected Closure $value)
    {
    }

    public function resolve(mixed ...$args): mixed
    {
        return ($this->value)(...$args);
    }

    public static function unwrap(mixed $data, mixed ...$args): mixed
    {
        if (is_array($data)) {
            return array_map(static fn($value) => self::unwrap($value, ...$args), $data);
        }

        return $data instanceof self ? $data->resolve(...$args) : $data;
    }
}
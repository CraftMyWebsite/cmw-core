<?php

namespace CMW\Manager\Requests;

enum HttpMethodsType
{
    case GET;
    case POST;
    case PUT;
    case HEAD;
    case PATCH;
    case DELETE;

    public static function fromName(string $name): HttpMethodsType
    {
        foreach (self::cases() as $method) {
            if ($name === $method->name) {
                return $method;
            }
        }
        throw new \ValueError("$name is not a valid backing value for enum HttpMethodsType");
    }
}

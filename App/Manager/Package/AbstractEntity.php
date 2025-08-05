<?php

namespace CMW\Manager\Package;

use CMW\Utils\Utils;
use InvalidArgumentException;
use JsonException;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use RuntimeException;
use function array_map;
use function class_exists;
use function is_array;
use function is_object;
use function json_decode;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;
use function method_exists;
use const JSON_ERROR_NONE;
use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;

abstract class AbstractEntity
{
    /**
     * @throws ReflectionException
     */
    public static function toEntity(array $brutData): static
    {
        // Replace snake_case to camelCase
        $data = [];
        foreach ($brutData as $key => $value) {
            $data[Utils::snakeToCamelCase($key)] = $value;
        }

        $reflector = new ReflectionClass(static::class);
        $constructor = $reflector->getConstructor();
        $parameters = $constructor?->getParameters() ?? [];

        $arguments = [];
        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            $type = $parameter->getType();

            if (!$type instanceof ReflectionNamedType) {
                throw new RuntimeException("The $name parameter must have a type. Entity: " . static::class);
            }

            $expectedType = $type->getName();

            if (isset($data[$name])) {
                $value = $data[$name];

                if ($expectedType === 'array' && $parameter->getType()?->getName() === 'array') {
                    $attributes = $parameter->getAttributes(EntityType::class);
                    if (!empty($attributes)) {
                        $entityClass = $attributes[0]->getArguments()[0];

                        if (!class_exists($entityClass) || !method_exists($entityClass, 'toEntity')) {
                            throw new RuntimeException("Unable to convert $name to entity. Entity: " . static::class);
                        }

                        $value = array_map([$entityClass, 'toEntity'], $value);
                    }
                } elseif (class_exists($expectedType) && method_exists($expectedType, 'toEntity')) {
                    $value = $expectedType::toEntity($value);
                }

                $arguments[] = $value;
            } elseif ($parameter->isOptional() || $parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();
            } elseif ($parameter->allowsNull()) {
                $arguments[] = null;
            } else {
                throw new RuntimeException("The $name ($type) parameter is required. Entity: " . static::class);
            }
        }

        return $reflector->newInstanceArgs($arguments);
    }

    /**
     * @param array $brutData
     * @return static[]
     * @throws ReflectionException
     */
    public static function toEntityList(array $brutData): array
    {
        $toReturn = [];
        foreach ($brutData as $item) {
            $toReturn[] = self::toEntity($item);
        }
        return $toReturn;
    }

    /**
     * @throws ReflectionException
     * @throws JsonException
     */
    public static function fromJson(string $json): static
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException(json_last_error_msg());
        }

        return self::toEntity($data);
    }

    /**
     * @param string $json
     * @return static[]
     * @throws JsonException
     * @throws ReflectionException
     */
    public static function fromJsonList(string $json): array
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException(json_last_error_msg());
        }

        return self::toEntityList($data);
    }

    /**
     * @throws JsonException
     */
    public function toJson(): string
    {
        $properties = $this->toArray();
        return json_encode($properties, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }

    /**
     * @param static[] $data
     * @throws JsonException
     */
    public static function toJsonList(array $data): string
    {
        $toReturn = [];
        foreach ($data as $item) {
            $toReturn[] = $item->toArray();
        }
        return json_encode($toReturn, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }

    /**
     * @return array
     * @desc Return all properties of the object
     */
    public function toArray(): array
    {
        $reflector = new ReflectionClass($this);
        $properties = $reflector->getProperties(ReflectionProperty::IS_PRIVATE);

        $data = [];
        foreach ($properties as $property) {
            $value = $property->getValue($this);
            if (is_object($value) && method_exists($value, 'toArray')) {
                $data[$property->getName()] = $value->toArray();
            } elseif (is_array($value)) {
                $data[$property->getName()] = array_map(static function ($item) {
                    return is_object($item) && method_exists($item, 'toArray') ? $item->toArray() : $item;
                }, $value);
            } else {
                $data[$property->getName()] = $value;
            }
        }

        return $data;
    }

    /**
     * @param static[] $data
     * @return array
     */
    public static function fromEntitiesToArray(array $data): array
    {
        $toReturn = [];
        foreach ($data as $item) {
            $toReturn[] = $item->toArray();
        }
        return $toReturn;
    }
}

<?php

namespace CMW\Manager\Package;

use CMW\Utils\Utils;
use InvalidArgumentException;
use JsonException;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use RuntimeException;
use function json_decode;
use function json_encode;
use function json_last_error;
use function json_last_error_msg;
use const JSON_ERROR_NONE;
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
        $parameters = $constructor?->getParameters();

        $arguments = [];
        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            if (isset($data[$name])) {
                $arguments[] = $data[$name];
            } else if ($parameter->isOptional()) {
                $arguments[] = $parameter->getDefaultValue();
            } else {
                throw new RuntimeException("Parameter $name is required");
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
            $data[$property->getName()] = $property->getValue($this);
        }

        return $data;
    }
}

<?php

namespace CMW\Manager\ORM\Attributes;

use Attribute;
use JetBrains\PhpStorm\ExpectedValues;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Column
{

    public function __construct(
        private readonly string $name,
        private readonly ColumnType $type,
        private readonly bool $nullable = false,
        private ?string $propertyName = null
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ColumnType
    {
        return $this->type;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function setPropertyName(string $propertyName): void
    {
        $this->propertyName = $propertyName;
    }

    public function getPropertyName(): ?string
    {
        return $this->propertyName;
    }

}
<?php

namespace CMW\Manager\ORM\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class Column
{

    public function __construct(
        private string $name,
        private string $type,
        private bool $nullable = false,
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

}
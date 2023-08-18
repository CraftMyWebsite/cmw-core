<?php

namespace CMW\Manager\ORM\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class Table
{

    public function __construct(
        private string $name
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }
}
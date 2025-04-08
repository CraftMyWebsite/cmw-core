<?php

namespace CMW\Manager\Package;

use Attribute;
use InvalidArgumentException;

#[Attribute(Attribute::TARGET_PARAMETER)]
class EntityType
{
    public function __construct(public string $entityClass)
    {
        if (!class_exists($this->entityClass)) {
            throw new InvalidArgumentException("The Entity class ($this->entityClass) doesn't exist.");
        }
    }
}

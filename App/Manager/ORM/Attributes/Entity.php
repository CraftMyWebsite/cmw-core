<?php

namespace CMW\Manager\ORM\Attributes;

use Attribute;
use CMW\Manager\Package\AbstractEntity;
use JetBrains\PhpStorm\ExpectedValues;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class Entity
{

    public function __construct(
        #[ExpectedValues(AbstractEntity::class)] private string $clazz
    )
    {
    }

    public function getEntity(): string
    {
        return $this->clazz;
    }

}
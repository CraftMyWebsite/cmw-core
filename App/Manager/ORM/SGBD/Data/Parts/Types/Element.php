<?php

namespace CMW\Manager\ORM\SGBD\Data\Parts\Types;

readonly class Element
{

    public function __construct(
        protected string $name,
        protected ?string $alias
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

}
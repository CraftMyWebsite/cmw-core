<?php

namespace CMW\Manager\ORM\SGBD\Data\Parts\Types\Join;

readonly class JoinColumn
{

    public function __construct(
        private string $mainColumn,
        private string $joinColumn
    )
    {
    }

    public function getMainColumn(): string
    {
        return $this->mainColumn;
    }

    public function getJoinColumn(): string
    {
        return $this->joinColumn;
    }

}
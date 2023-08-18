<?php

namespace CMW\Manager\ORM\SGBD\Data\Parts\Types\Join;

use CMW\Manager\ORM\SGBD\Data\Parts\Types\Table;

readonly class JoinTable
{

    public function __construct(
        private Table       $table,
        private ?JoinType   $type,
        private ?JoinColumn $joinColumn,
    )
    {
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function getType(): ?JoinType
    {
        return $this->type;
    }

    public function getJoinColumn(): ?JoinColumn
    {
        return $this->joinColumn;
    }

}
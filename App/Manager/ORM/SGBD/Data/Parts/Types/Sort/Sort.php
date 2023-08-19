<?php

namespace CMW\Manager\ORM\SGBD\Data\Parts\Types\Sort;

readonly class Sort
{

    public function __construct(
        private array $columns,
        private SortType $type
    )
    {
    }

    public function __debugInfo(): ?array
    {
        return [
            'columns' => $this->columns,
            'type' => $this->type->name
        ];
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getType(): SortType
    {
        return $this->type;
    }

}
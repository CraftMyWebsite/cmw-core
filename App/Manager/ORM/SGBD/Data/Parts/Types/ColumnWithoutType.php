<?php

namespace CMW\Manager\ORM\SGBD\Data\Parts\Types;

readonly class ColumnWithoutType extends Element
{
    public function __debugInfo(): ?array
    {
        return [
            'name' => $this->name,
            'alias' => $this->alias
        ];
    }

}
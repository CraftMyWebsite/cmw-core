<?php

namespace CMW\Manager\ORM\SGBD\Data\Parts\Types;

readonly class Column extends Element
{

    public function __debugInfo(): ?array
    {
        return [
            'name' => $this->name,
            'alias' => $this->alias
        ];
    }

}
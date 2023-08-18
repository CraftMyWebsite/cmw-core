<?php

namespace CMW\Manager\ORM\SGBD\Data\Parts\Types\Where;

readonly class Where
{

    public function __construct(
        private string $column,
        private WhereOperator $operator,
        private int|string|array|null $value = null
    )
    {
    }

    public function __debugInfo(): ?array
    {
        return [
            'column' => $this->column,
            'operator' => $this->operator->name,
            'value' => $this->value
        ];
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function getOperator(): WhereOperator
    {
        return $this->operator;
    }

    public function getValue(): int|string|null
    {
        return $this->value;
    }

}
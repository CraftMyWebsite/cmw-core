<?php

namespace CMW\Manager\ORM\SGBD\Data\Parts;

use CMW\Manager\ORM\SGBD\Data\Parts\Types\Actions;

class Statement
{

    /**
     * @var Types\Column[] $columns
     */
    private array $columns = [];

    public function __construct(
        private readonly Actions $action
    )
    {
    }

    public function __debugInfo(): ?array
    {
        $columnList = array();
        foreach($this->columns as $column) {
            $columnList[] = $column->getName() . ' AS ' . ($column->getAlias() ?? 'NO_ALIAS');
        }

        return [
            'columns' => $columnList
        ];
    }

    public function addColumn(string $column, ?string $alias= null): void
    {
        $col = new Types\Column($column, $alias);
        if(!in_array($col, $this->columns, true)) {
            $this->columns[] = $col;
        }
    }

    public function getColumns(): array
    {
        return empty($this->columns) ? ['*'] : $this->columns;
    }

    public function getAction(): Actions
    {
        return $this->action;
    }
}
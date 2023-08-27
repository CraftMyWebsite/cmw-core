<?php

namespace CMW\Manager\ORM\SGBD\Data\Parts;

use CMW\Manager\ORM\Attributes\Column;
use CMW\Manager\Package\AbstractEntity;

class EntityObject
{

    /**
     * @var class-string<AbstractEntity> $clazzEntity
     */
    private readonly string $clazzEntity;

    /**
     * @var Column[] $columns
     */
    private array $columns = [];


    public function __construct(
        string $clazzEntity
    )
    {
        $this->clazzEntity = $clazzEntity;
    }

    public function addColumns(Column ...$columns): void
    {
        foreach ($columns as $column) {
            $this->addColumn($column);
        }
    }

    public function addColumn(Column $column): void
    {
        $this->columns[] = $column;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return class-string<AbstractEntity>
     */
    public function getClazz(): string
    {
        return $this->clazzEntity;
    }


}
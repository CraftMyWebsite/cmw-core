<?php

namespace CMW\Manager\ORM\SGBD\Data\Parts;

use CMW\Manager\ORM\SGBD\Data\Parts\Types\Join\JoinColumn;
use CMW\Manager\ORM\SGBD\Data\Parts\Types\Join\JoinTable;
use CMW\Manager\ORM\SGBD\Data\Parts\Types\Join\JoinType;
use CMW\Manager\ORM\SGBD\Data\Parts\Types\Table;

class Location
{

    private Table $mainTable;
    /**
     * @var JoinTable[] $joinTable
     */
    private array $joinTable = [];

    public function __construct(
        private readonly string $tableName,
        private readonly ?string $alias
    )
    {
        $this->mainTable = new Table($tableName, $alias);
    }

    public function __debugInfo(): ?array
    {
        $debug = array();
        $debug['mainTable'] = $this->mainTable->getName() . ' AS ' . ($this->mainTable->getAlias() ?? 'NO_ALIAS');
        if(!empty($this->joinTable)) {
            $debug['joinTable'] = $this->joinTable;
        }
        return $debug;
    }

    public function getTable(): Table
    {
        return $this->mainTable;
    }

    public function getJoinTableList(): array
    {
        return $this->joinTable;
    }

    public function addJoinTable(string $tableName, ?string $alias, string $mainColumn, ?string $joinColumn, JoinType $type = JoinType::INNER): void
    {
        if($joinColumn === null) {
            $joinColumn = $mainColumn;
        }

        $table = new Table($tableName, $alias);
        $column = new JoinColumn($mainColumn, $joinColumn);
        $this->joinTable[] = new JoinTable(
            $table,
            $type,
            $column,
        );
    }

}
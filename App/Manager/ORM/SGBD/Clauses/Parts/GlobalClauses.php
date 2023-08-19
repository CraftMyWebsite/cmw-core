<?php

namespace CMW\Manager\ORM\SGBD\Clauses\Parts;

use CMW\Manager\ORM\SGBD\Clauses\AbstractClauses;
use CMW\Manager\ORM\SGBD\Clauses\ConstructClause;
use CMW\Manager\ORM\SGBD\Execute;

class GlobalClauses extends AbstractClauses
{

    use ConstructClause;

    public function where(string $column): WhereClauses
    {
        return new WhereClauses($this->_actionsInstance, $column);
    }

    public function order(string ...$column) : SortClauses
    {
        $columnList = [...$column];
        return new SortClauses($this->_actionsInstance, $columnList);
    }

    public function limit(int $int): Execute
    {
        return (new LimitClauses($this->_actionsInstance))->limit($int);
    }

}
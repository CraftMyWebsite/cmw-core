<?php

namespace CMW\Manager\ORM\SGBD\Clauses\Parts;

use CMW\Manager\ORM\SGBD\Clauses\AbstractClauses;
use CMW\Manager\ORM\SGBD\Clauses\ConstructClause;

class ReadClauses extends AbstractClauses
{

    use ConstructClause;

    public function distinct(): ReadClauses
    {
        echo "distinct";
        return $this;
    }

    public function all(): GlobalClauses
    {
        echo "all <br>";
        return new GlobalClauses($this->_actionsInstance);
    }

    public function columns(string ...$columns): GlobalClauses
    {
        foreach ($columns as $column) {
            $this->_actionsInstance->getORM()->getReceiver()->getStatement()->addColumn($column, null);
        }
        return new GlobalClauses($this->_actionsInstance);
    }

}
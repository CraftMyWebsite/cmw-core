<?php

namespace CMW\Manager\ORM\SGBD\Clauses;

use CMW\Manager\ORM\SGBD\Actions;

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
        echo "all";
        return new GlobalClauses($this->_actionsInstance);
    }

}
<?php

namespace CMW\Manager\ORM\SGBD\Clauses;

use CMW\Manager\ORM\SGBD\Actions;
use CMW\Manager\ORM\SGBD\Execute;

class GlobalClauses extends AbstractClauses
{

    use ConstructClause;

    public function where(): GlobalClauses
    {
        echo "where";
        return $this;
    }

    public function limit(): Execute
    {
        echo "limit";
        return new Execute($this);
    }

}
<?php

namespace CMW\Manager\ORM\SGBD\Clauses\Parts;

use CMW\Manager\ORM\SGBD\Clauses\AbstractClauses;
use CMW\Manager\ORM\SGBD\Clauses\ConstructClause;
use CMW\Manager\ORM\SGBD\Execute;

class LimitClauses extends AbstractClauses
{

    use ConstructClause;

    public function limit(int $int): Execute
    {
        if ($int > 0) {
            $this->_actionsInstance->getORM()->getReceiver()->getFilter()->setLimit($int);
        }
        return new Execute($this);
    }

}
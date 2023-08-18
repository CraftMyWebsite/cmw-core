<?php

namespace CMW\Manager\ORM\SGBD\Clauses\Parts;

use CMW\Manager\ORM\SGBD\Clauses\AbstractClauses;
use CMW\Manager\ORM\SGBD\Clauses\ConstructClause;
use CMW\Manager\ORM\SGBD\Data\Parts\Types\SortType;

class SortClauses extends AbstractClauses
{

    use ConstructClause;

    public function asc(): GlobalClauses
    {
        $this->_actionsInstance->getORM()->getReceiver()->getFilter()->setSortType(SortType::ASC);
        return new GlobalClauses($this->_actionsInstance);
    }

    public function desc(): GlobalClauses
    {
        $this->_actionsInstance->getORM()->getReceiver()->getFilter()->setSortType(SortType::DESC);
        return new GlobalClauses($this->_actionsInstance);
    }

}
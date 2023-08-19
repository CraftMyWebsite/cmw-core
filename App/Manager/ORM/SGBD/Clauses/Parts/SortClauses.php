<?php

namespace CMW\Manager\ORM\SGBD\Clauses\Parts;

use CMW\Manager\ORM\SGBD\Actions;
use CMW\Manager\ORM\SGBD\Clauses\AbstractClauses;
use CMW\Manager\ORM\SGBD\Clauses\ConstructClause;
use CMW\Manager\ORM\SGBD\Data\Parts\Types\Sort\SortType;

class SortClauses extends AbstractClauses
{

    use ConstructClause;

    public function __construct(
        protected Actions $_actionsInstance,
        private readonly array $columns
    )
    {
    }

    public function asc(): GlobalClauses
    {
        $this->_actionsInstance->getORM()->getReceiver()->getFilter()->addSort($this->columns, SortType::ASC);
        return new GlobalClauses($this->_actionsInstance);
    }

    public function desc(): GlobalClauses
    {
        $this->_actionsInstance->getORM()->getReceiver()->getFilter()->addSort($this->columns, SortType::DESC);
        return new GlobalClauses($this->_actionsInstance);
    }

}
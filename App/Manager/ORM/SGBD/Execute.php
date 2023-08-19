<?php

namespace CMW\Manager\ORM\SGBD;

use CMW\Manager\ORM\SGBD\Clauses\AbstractClauses;

readonly class Execute
{

    public function __construct(
        private AbstractClauses $_clauseInstance
    )
    {
    }

    public function execute(): array
    {
        return $this->_clauseInstance->execute();
    }

}
<?php

namespace CMW\Manager\ORM\SGBD;

use CMW\Manager\ORM\ORM;
use CMW\Manager\ORM\SGBD\Clauses as Clauses;

readonly class Actions
{

    public function __construct(
        private ORM $_ormInstance
    )
    {
    }

    public function read(): Clauses\ReadClauses
    {
        return new Clauses\ReadClauses($this);
    }

    public function execute(): array
    {
        return $this->_ormInstance->execute();
    }
}
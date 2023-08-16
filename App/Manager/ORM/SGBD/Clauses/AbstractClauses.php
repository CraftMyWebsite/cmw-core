<?php

namespace CMW\Manager\ORM\SGBD\Clauses;

use CMW\Manager\ORM\SGBD\Actions;

abstract class AbstractClauses
{
    protected Actions $_actionsInstance;

    public function execute(): array
    {
        return $this->_actionsInstance->execute();
    }
}
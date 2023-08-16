<?php

namespace CMW\Manager\ORM\SGBD\Clauses;

use CMW\Manager\ORM\SGBD\Actions;

trait ConstructClause
{
    public function __construct(
        protected Actions $_actionsInstance
    )
    {
    }
}
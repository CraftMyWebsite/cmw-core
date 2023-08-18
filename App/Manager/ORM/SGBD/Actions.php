<?php

namespace CMW\Manager\ORM\SGBD;

use CMW\Manager\ORM\ORM;
use CMW\Manager\ORM\SGBD\Clauses as Clauses;
use CMW\Manager\ORM\SGBD\Data\Parts\Statement;
use CMW\Manager\ORM\SGBD\Data\Parts\Types\Table;
use CMW\Manager\Package\AbstractEntity;
use JetBrains\PhpStorm\ExpectedValues;

readonly class Actions
{

    public function __construct(
        private ORM $_ormInstance
    )
    {
    }

    public function read(#[ExpectedValues(AbstractEntity::class)] string $entityClazz): Clauses\Parts\ReadClauses
    {
        $this->_ormInstance->getReceiver()->setLocation(
            new Table(
                "test",
                ""
            )
        );
        $this->_ormInstance->getReceiver()->setStatement(
            new Statement(
                Data\Parts\Types\Actions::SELECT
            )
        );
        return new Clauses\Parts\ReadClauses($this);
    }

    public function getORM(): ORM
    {
        return $this->_ormInstance;
    }

    public function execute(): array
    {
        return $this->_ormInstance->execute();
    }
}
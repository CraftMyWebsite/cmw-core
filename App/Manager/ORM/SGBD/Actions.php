<?php

namespace CMW\Manager\ORM\SGBD;

use CMW\Manager\ORM\ORM;
use CMW\Manager\ORM\SGBD\Clauses as Clauses;
use CMW\Manager\ORM\SGBD\Data\EntityReader;
use CMW\Manager\ORM\SGBD\Data\Parts\Statement;
use CMW\Manager\ORM\SGBD\Data\Parts\Types\Table;
use CMW\Manager\Package\AbstractEntity;
use JetBrains\PhpStorm\ExpectedValues;
use ReflectionException;
use RuntimeException;

/**
 * @template T of AbstractEntity
 */
readonly class Actions
{

    public function __construct(
        private ORM $_ormInstance
    )
    {
    }

    /**
     * @param class-string<T> $entityClazz
     * @return Clauses\Parts\ReadClauses
     */
    public function read(#[ExpectedValues(AbstractEntity::class)] string $entityClazz): Clauses\Parts\ReadClauses
    {
        $entityChecker = new EntityReader($this->_ormInstance, $entityClazz);

        $this->_ormInstance->getReceiver()->setEntityObject($entityClazz);

        $this->_ormInstance->getReceiver()->getEntityObject()->addColumns(...$entityChecker->getColumns());

        $this->_ormInstance->getReceiver()->setLocation(
            $entityChecker->getTable()
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

    public function execute(): ORM
    {
        return $this->_ormInstance->execute();
    }
}
<?php

namespace CMW\Manager\ORM\SGBD;

use CMW\Manager\ORM\ORM;
use CMW\Manager\ORM\SGBD\Clauses as Clauses;
use CMW\Manager\ORM\SGBD\Data\Parts\Statement;
use CMW\Manager\ORM\SGBD\Data\Parts\Types\Table;
use CMW\Manager\Package\AbstractEntity;
use JetBrains\PhpStorm\ExpectedValues;
use ReflectionException;
use RuntimeException;

readonly class Actions
{

    public function __construct(
        private ORM $_ormInstance
    )
    {
    }

    public function read(#[ExpectedValues(AbstractEntity::class)] string $entityClazz): Clauses\Parts\ReadClauses
    {

        try {
            $entity = new \ReflectionClass($entityClazz);
            $table = $entity->getAttributes(\CMW\Manager\ORM\Attributes\Table::class);
            if(empty($table)) {
                throw new RuntimeException("The entity class $entityClazz does not have a table attribute");
            }

            $tableInstance = $table[0]->newInstance();

            $this->_ormInstance->getReceiver()->setLocation(
                new Table(
                    $tableInstance->getName()
                )
            );
        } catch (ReflectionException $e) {
            throw new RuntimeException("The entity class $entityClazz does not exist");
        }

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
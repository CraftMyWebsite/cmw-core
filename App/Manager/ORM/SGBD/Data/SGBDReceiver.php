<?php

namespace CMW\Manager\ORM\SGBD\Data;

use CMW\Manager\ORM\SGBD\Data\Parts\EntityObject;
use CMW\Manager\ORM\SGBD\Data\Parts\Filter;
use CMW\Manager\ORM\SGBD\Data\Parts\Location;
use CMW\Manager\ORM\SGBD\Data\Parts\Statement;
use CMW\Manager\ORM\SGBD\Data\Parts\Types\Table;
use CMW\Manager\Package\AbstractEntity;

class SGBDReceiver
{

    private ?Filter $filter = null;
    private Location $location;
    private Statement $statement;
    private EntityObject $entityObject;

    public function __construct()
    {
    }

    public function getFilter(): Filter
    {
        if ($this->filter === null) {
            $this->filter = new Filter();
        }
        return $this->filter;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function getStatement(): Statement
    {
        return $this->statement;
    }

    public function getEntityObject(): EntityObject
    {
        return $this->entityObject;
    }

    public function setLocation(Table $table): void
    {
        $this->location = new Location($table->getName(), $table->getAlias());
    }

    public function setStatement(Statement $statement): void
    {
        $this->statement = $statement;
    }

    /**
     * @param class-string<AbstractEntity> $clazzEntity
     */
    public function setEntityObject(string $clazzEntity): void
    {
        $this->entityObject = new EntityObject($clazzEntity);
    }

}
<?php

namespace CMW\Manager\ORM;

use CMW\Manager\ORM\Attributes\Column;
use CMW\Manager\ORM\Database\DatabaseManager;
use CMW\Manager\ORM\Database\SGBD;
use CMW\Manager\ORM\SGBD\Actions;
use CMW\Manager\ORM\SGBD\Data\SGBDReceiver;
use CMW\Manager\Package\AbstractEntity;
use CMW\Utils\Log;
use Generator;

class ORM
{

    /** @var AbstractEntity[] $result  */
    private array $result = [];



    private function __construct(
        private readonly ?SGBD $_sgbdInstance,
        private readonly SGBDReceiver $_sgbdReceiver
    )
    {
    }

    public function getSGBD(): SGBD
    {
        return $this->_sgbdInstance;
    }

    public function getReceiver(): SGBDReceiver
    {
        return $this->_sgbdReceiver;
    }

    public function execute(): self
    {
        $result =  $this->getSGBD()->generate($this->getReceiver());

        /** @var AbstractEntity[] $toSend */
        $toSend = array();

        foreach ($result as $lines) {
            $entity = New \ReflectionClass($this->getReceiver()->getEntityObject()->getClazz());
            $properties = [];
            foreach ($lines as $column => $value) {
                foreach ($this->getReceiver()->getEntityObject()->getColumns() as $columnList) {
                    if ($columnList->getName() === $column) {
                        $properties[$columnList->getPropertyName()] = $value;
                    }
                }
            }

            $toSend[] = $entity->newInstanceArgs($properties);
        }

        $this->result = $toSend;
        return $this;
    }

    public function fetchOne(): ?AbstractEntity
    {
        return array_pop($this->result);
    }

    public function fetchAll(): Generator
    {
        foreach ($this->result as $entity) {
            yield $entity;
        }
    }

    /**
     * @return Column[]
     */
    public function describe(string $table): array
    {
        return $this->getSGBD()->describe($table);
    }

    public static function getInstance(?SGBD $sgbdInstance = null): Actions
    {
        $sgbd = $sgbdInstance ?? DatabaseManager::getInstance();

        if ($sgbd === null) {
            throw new \Exception("No SGBD instance found");
        }

        return new Actions(new self($sgbd, new SGBDReceiver()));
    }

}
<?php

namespace CMW\Manager\ORM\SGBD\Data;

use CMW\Manager\ORM\Attributes\Column;
use CMW\Manager\ORM\Attributes\Table as TableAttribute;
use CMW\Manager\ORM\ORM;
use CMW\Manager\ORM\SGBD\Data\Parts\Types\Table;
use CMW\Manager\Package\AbstractEntity;
use Exception;
use JetBrains\PhpStorm\ExpectedValues;
use ReflectionClass;

class EntityReader
{

    private Table $table;
    /**
     * @var Column[] $columns
     */
    private array $columns;

    public function __construct(
        private ORM                                                      $_ormInstance,
        #[ExpectedValues(AbstractEntity::class)] private readonly string $entityClazz
    )
    {
        $this->checkEntity();
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    private function checkEntity(): void
    {
        if (!class_exists($this->entityClazz)) {
            throw new Exception("Entity class $this->entityClazz not found");
        }

        if (!is_subclass_of($this->entityClazz, AbstractEntity::class)) {
            throw new Exception("Entity class $this->entityClazz must extends AbstractEntity");
        }

        $reflectionClass = new ReflectionClass($this->entityClazz);
        $attributes = $reflectionClass->getAttributes(TableAttribute::class);

        if (empty($attributes)) {
            throw new Exception("Entity class $this->entityClazz must have a Table attribute");
        }

        $this->table = new Table(
            $attributes[0]->newInstance()->getName()
        );

        foreach ($reflectionClass->getProperties() as $property) {
            foreach ($property->getAttributes() as $attribute) {
                if ($attribute->getName() === Column::class) {
                    /** @var Column $columnAttr */
                    $columnAttr = $attribute->newInstance();
                    $columnAttr->setPropertyName($property->getName());
                    $this->columns[] = $columnAttr;

                }
            }
        }

        if (empty($this->columns)) {
            throw new Exception("Entity class $this->entityClazz must have at least one ColumnWithoutType attribute");
        }

        $columnListForTable = $this->_ormInstance->describe($this->table->getName());

        //Count columns in columnListForTable when column is not nullable
        $count = 0;
        array_map(static function ($column) use (&$count) {
            if (!$column->isNullable()) {
                $count++;
            }
        }, $columnListForTable);

        if (count($this->columns) < $count) {
            throw new Exception("Entity class $this->entityClazz must have at least " . $count . " Column attribute");
        }
    }

}
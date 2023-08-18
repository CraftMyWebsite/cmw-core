<?php

namespace CMW\Manager\ORM\SGBD\Clauses\Parts;

use CMW\Manager\ORM\SGBD\Actions;
use CMW\Manager\ORM\SGBD\Data\Parts\Filter;
use CMW\Manager\ORM\SGBD\Data\Parts\Types\Where\WhereOperator;

class WhereClauses
{

    private Filter $filter;

    public function __construct(
        protected Actions       $_actionsInstance,
        private readonly string $column
    )
    {
        $this->filter = $this->_actionsInstance->getORM()->getReceiver()->getFilter();
    }

    public function isNull(): GlobalClauses
    {
        $this->filter->addWhere($this->column, WhereOperator::IS_NULL, null);
        return new GlobalClauses($this->_actionsInstance);
    }

    public function isNotNull(): GlobalClauses
    {
        $this->filter->addWhere($this->column, WhereOperator::IS_NOT_NULL, null);
        return new GlobalClauses($this->_actionsInstance);
    }

    public function equals(int|string $value): GlobalClauses
    {
        $this->filter->addWhere($this->column, WhereOperator::EQUALS, $value);
        return new GlobalClauses($this->_actionsInstance);
    }

    public function different(int|string $value): GlobalClauses
    {
        $this->filter->addWhere($this->column, WhereOperator::NOT_EQUALS, $value);
        return new GlobalClauses($this->_actionsInstance);
    }

    public function greater(int|string $value): GlobalClauses
    {
        $this->filter->addWhere($this->column, WhereOperator::GREATER_THAN, $value);
        return new GlobalClauses($this->_actionsInstance);
    }

    public function greaterOrEquals(int|string $value): GlobalClauses
    {
        $this->filter->addWhere($this->column, WhereOperator::GREATER_THAN_OR_EQUALS, $value);
        return new GlobalClauses($this->_actionsInstance);
    }

    public function less(int|string $value): GlobalClauses
    {
        $this->filter->addWhere($this->column, WhereOperator::LESS_THAN, $value);
        return new GlobalClauses($this->_actionsInstance);
    }

    public function lessOrEquals(int|string $value): GlobalClauses
    {
        $this->filter->addWhere($this->column, WhereOperator::LESS_THAN_OR_EQUALS, $value);
        return new GlobalClauses($this->_actionsInstance);
    }

    public function between(int|string $firstValue, int|string $secondValue): GlobalClauses
    {
        $this->filter->addWhere($this->column, WhereOperator::BETWEEN, [$firstValue, $secondValue]);
        return new GlobalClauses($this->_actionsInstance);
    }

    public function like(string $value): GlobalClauses
    {
        $this->filter->addWhere($this->column, WhereOperator::LIKE, $value);
        return new GlobalClauses($this->_actionsInstance);
    }

    public function in(array $values): GlobalClauses
    {
        $this->filter->addWhere($this->column, WhereOperator::IN, $values);
        return new GlobalClauses($this->_actionsInstance);
    }

}
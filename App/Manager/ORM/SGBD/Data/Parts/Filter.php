<?php

namespace CMW\Manager\ORM\SGBD\Data\Parts;

use CMW\Manager\ORM\SGBD\Data\Parts\Types\Sort\Sort;
use CMW\Manager\ORM\SGBD\Data\Parts\Types\Sort\SortType;
use CMW\Manager\ORM\SGBD\Data\Parts\Types\Where\Where;
use CMW\Manager\ORM\SGBD\Data\Parts\Types\Where\WhereOperator;

class Filter
{

    /**
     * @var Where[] $where
     */
    private array $where = [];

    /**
     * @var Sort[] $sort
     */
    private array $sort = [];
    private int $limit = 0;

    public function __construct()
    {
    }

    public function __debugInfo(): ?array
    {
        $sortList = [];
        foreach ($this->sort as $sort) {
            $sortList[] = implode(', ', $sort->getColumn()) . ' ' . $sort->getType()->name;
        }

        return [
            'where' => $this->where,
            'sort' => $sortList,
            'limit' => $this->limit
        ];
    }

    public function getWhere(): array
    {
        return $this->where;
    }

    /**
     * @return Sort[]
     */
    public function getSortList(): array
    {
        return $this->sort;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function addWhere(string $column, WhereOperator $operator, int|string|array|null $value = null): void
    {
        $this->where[] = new Where($column, $operator, $value);
    }

    public function addSort(array $column, SortType $type): void
    {
        $this->sort[] = new Sort($column, $type);
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

}
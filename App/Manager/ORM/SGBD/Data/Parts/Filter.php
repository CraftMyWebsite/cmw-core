<?php

namespace CMW\Manager\ORM\SGBD\Data\Parts;

use CMW\Manager\ORM\SGBD\Data\Parts\Types\SortType;
use CMW\Manager\ORM\SGBD\Data\Parts\Types\Where\Where;
use CMW\Manager\ORM\SGBD\Data\Parts\Types\Where\WhereOperator;

class Filter
{

    /**
     * @var Where[] $where
     */
    private array $where = [];
    private array $sort = [];
    private SortType $sortType;
    private int $limit = 0;

    public function __construct()
    {
    }

    public function __debugInfo(): ?array
    {
        return [
            'where' => $this->where,
            'sort' => implode($this->sort),
            'sortType' => $this->sortType->name,
            'limit' => $this->limit
        ];
    }

    public function getWhere(): array
    {
        return $this->where;
    }

    public function getSortList(): array
    {
        return $this->sort;
    }

    public function getSortType(): SortType
    {
        return $this->sortType;
    }

    public function addWhere(string $column, WhereOperator $operator, int|string|array|null $value = null): void
    {
        $this->where[] = new Where($column, $operator, $value);
    }

    public function addSort(string ...$sort): void
    {
        foreach ($sort as $s) {
            if (!in_array($s, $this->sort, true)) {
                $this->sort[] = $s;
            }
        }
    }

    public function setSortType(SortType $sortType): void
    {
        $this->sortType = $sortType;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

}
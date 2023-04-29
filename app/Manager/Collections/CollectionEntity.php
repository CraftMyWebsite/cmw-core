<?php

namespace CMW\Manager\Collections;

use Generator;

class CollectionEntity
{

    public function __construct(
        public readonly string $name,
        private array           $_data = array()
    )
    {
    }

    public function add(mixed $value): self
    {
        $this->_data[] = $value;
        return $this;
    }

    public function addWithKey(string $key, mixed $value): self
    {
        $this->_data[$key] = $value;
        return $this;
    }

    public function iterate(): Generator
    {
        foreach ($this->_data as $dataName => $dataValue) {
            yield $dataName => $dataValue;
        }
    }

    public function get(mixed $value): array
    {
        return array_filter($this->_data, static function (object $v) use ($value) {
            return $v === $value;
        });
    }

    public function getWithKey(string $key): mixed {
        return $this->_data[$key] ?? null;
    }

}
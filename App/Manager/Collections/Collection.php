<?php

namespace CMW\Manager\Collections;

class Collection
{

    /** @var \CMW\Manager\Collections\CollectionEntity[] */
    private array $_array = array();
    private static Collection $_instance;

    private function getGlobalCollection(): array
    {
        return $this->_array;
    }

    public static function getInstance(): Collection
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function hasCollection(string $collectionName): bool
    {
        return array_key_exists($collectionName, $this->getGlobalCollection());
    }

    public function create(string $collectionName): self
    {
        if (!$this->hasCollection($collectionName)) {
            $this->_array[$collectionName] = new CollectionEntity($collectionName);
        }

        return $this;
    }

    public function get(string $collectionName): CollectionEntity
    {
        if (!$this->hasCollection($collectionName)) {
            $this->create($collectionName);
        }

        return $this->getGlobalCollection()[$collectionName];
    }

    public function remove(string $collectionName): self
    {

        if ($this->hasCollection($collectionName)) {
            unset($this->getGlobalCollection()[$collectionName]);
        }

        return $this;
    }

}
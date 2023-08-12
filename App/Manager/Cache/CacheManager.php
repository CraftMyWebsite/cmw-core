<?php

namespace CMW\Manager\Cache;

class CacheManager implements Cache
{

    private ?Cache $selectedCache;
    private static ?CacheManager $instance;

    public static function getInstance(): self {
        if(is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function setCache(string $cacheName): void {
        $this->selectedCache = match ($cacheName) {
            FileCache::CACHE_NAME => new FileCache(),
            default => null,
        };
    }

    public function get(string $key): mixed
    {
        return $this->selectedCache->get($key);
    }

    public function add(string $key, string $value): mixed
    {
        return $this->selectedCache->add($key, $value);
    }

    public function edit(string $key, string $value): mixed
    {
        return $this->selectedCache->edit($key, $value);
    }

    public function remove(string $key): bool
    {
        return $this->selectedCache->remove($key);
    }
}
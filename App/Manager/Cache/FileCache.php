<?php

namespace CMW\Manager\Cache;

class FileCache implements Cache
{

    public const CACHE_NAME = "file";

    public function get(string $key): mixed
    {
        // TODO: Implement get() method.
    }

    public function add(string $key, string $value): mixed
    {
        //add a file to /cache/ folder with the key as name and the value as content



    }

    public function edit(string $key, string $value): mixed
    {
        // TODO: Implement edit() method.
    }

    public function remove(string $key): bool
    {
        // TODO: Implement remove() method.
    }
}
<?php

namespace CMW\Manager\Cache;

interface Cache
{

    public function get(string $key): mixed;
    public function add(string $key, string $value): mixed;
    public function edit(string $key, string $value): mixed;
    public function remove(string $key): bool;

}
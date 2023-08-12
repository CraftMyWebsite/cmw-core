<?php

namespace CMW\Manager\ORM;

interface SGBD
{
    public function connect(): mixed;

    public function create(): void;
    public function read(): void;
    public function update(): void;
    public function delete(): void;
}
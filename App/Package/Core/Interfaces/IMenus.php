<?php

namespace CMW\Interface\Core;

interface IMenus
{
    public function getRoutes(): array;
    public function getPackageName(): String;
}

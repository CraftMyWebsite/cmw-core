<?php

namespace CMW\Implementation\Core;

use CMW\Interface\Core\IMenus;

class CoreMenusImplementations implements IMenus {

    public function getRoutes(): array
    {
        return [];
    }

    public function getPackageName(): string
    {
        return "Core";
    }
}
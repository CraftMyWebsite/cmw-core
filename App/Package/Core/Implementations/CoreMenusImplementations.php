<?php

namespace CMW\Implementation\Core;

use CMW\Interface\Core\IMenus;
use CMW\Manager\Lang\LangManager;

class CoreMenusImplementations implements IMenus
{

    public function getRoutes(): array
    {
        return [
            LangManager::translate('core.home') => 'home',
            LangManager::translate('core.cgu') => 'cgu',
            LangManager::translate('core.cgv') => 'cgv'
        ];
    }

    public function getPackageName(): string
    {
        return "Core";
    }
}
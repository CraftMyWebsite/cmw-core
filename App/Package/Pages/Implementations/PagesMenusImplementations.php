<?php

namespace CMW\Implementation\Pages;

use CMW\Interface\Core\IMenus;
use CMW\Model\Pages\PagesModel;

class PagesMenusImplementations implements IMenus {

    public function getRoutes(): array
    {
        $slug = [];

        foreach ((new PagesModel())->getPages() as $page) {
            $slug[$page->getTitle()] = $page->getSlug();
        }

        return $slug;
    }

    public function getPackageName(): string
    {
        return "Pages";
    }
}
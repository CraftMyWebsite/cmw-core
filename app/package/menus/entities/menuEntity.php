<?php

namespace CMW\Entity\Menus;

class menuEntity
{
    public array $menu;

    /**
     * @param array $menu
     */
    public function __construct(array $menu)
    {
        $this->menu = $menu;
    }

    /**
     * @return array
     */
    public function getMenu(): array
    {
        return $this->menu;
    }


}
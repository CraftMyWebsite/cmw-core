<?php

namespace CMW\Entity\Core;

class MenuEntity
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
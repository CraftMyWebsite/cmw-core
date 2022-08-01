<?php

namespace CMW\Manager\PackageInfo;

class Menu
{


    /**
     * @param array $menuName
     * @param string $menuIcon
     * @param string $menuURL
     * @param array $menuSubMenu
     */
    public function __construct(
        private readonly array  $menuName,
        private readonly string $menuIcon,
        private readonly string $menuURL,
        private readonly array  $menuSubMenu)
    {
    }

    public function getName(): array
    {
        return $this->menuName;
    }

    public function getIcon(): string
    {
        return $this->menuIcon;
    }

    public function getURL(): string
    {
        return $this->menuURL;
    }

    public function getSubMenu(): array
    {
        return $this->menuSubMenu;
    }




}
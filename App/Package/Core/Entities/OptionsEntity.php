<?php

namespace CMW\Entity\Core;

class OptionsEntity
{
    public string $theme;
    public array $menu;
    public string $name;
    public string $description;

    /**
     * @param string $theme
     * @param array $menu
     * @param string $name
     * @param string $description
     */
    public function __construct(string $theme, array $menu, string $name, string $description)
    {
        $this->theme = $theme;
        $this->menu = $menu;
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getTheme(): string
    {
        return $this->theme;
    }

    /**
     * @return array
     */
    public function getMenu(): array
    {
        return $this->menu;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}

<?php

namespace CMW\Entity\Core;

class PackageMenusEntity
{
    private string $name;
    private string $icon;
    private string $url;
    private array $submenu;

    /**
     * @param string $name
     * @param string $icon
     * @param string $url
     * @param array $submenu
     */
    public function __construct(string $name, string $icon, string $url, array $submenu)
    {
        $this->name = $name;
        $this->icon = $icon;
        $this->url = $url;
        $this->submenu = $submenu;
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
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return array
     */
    public function getSubmenu(): array
    {
        return $this->submenu;
    }
}

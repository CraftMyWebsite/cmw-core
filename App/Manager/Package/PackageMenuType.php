<?php

namespace CMW\Manager\Package;

class PackageMenuType
{
    private string $icon;
    private string $title;
    private ?string $url;
    private ?string $permission;

    /* @var PackageSubMenuType[]|null $subMenus */
    private array $subMenus;

    /**
     * @param string $icon
     * @param string $title
     * @param string|null $url
     * @param string|null $permission
     * @param PackageSubMenuType[] $subMenus
     */
    public function __construct(string $icon, string $title, ?string $url, ?string $permission, array $subMenus = [])
    {
        $this->icon = $icon;
        $this->title = $title;
        $this->url = $url;
        $this->permission = $permission;
        $this->subMenus = $subMenus;
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
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @return string|null
     */
    public function getPermission(): ?string
    {
        return $this->permission;
    }

    /**
     * @return array
     */
    public function getSubMenus(): array
    {
        return $this->subMenus;
    }
}

<?php

namespace CMW\Manager\Package;

class PackageSubMenuType
{
    private string $title;
    private string $permission;
    private ?string $url;

    /* @var \CMW\Manager\Package\PackageSubMenuType[]|null $subMenus */
    private ?array $subMenus;

    /**
     * @param string $title
     * @param string $permission
     * @param ?string $url
     * @param \CMW\Manager\Package\PackageSubMenuType[]|null $subMenus
     */
    public function __construct(string $title, string $permission, ?string $url, ?array $subMenus)
    {
        $this->title = $title;
        $this->permission = $permission;
        $this->url = $url;
        $this->subMenus = $subMenus;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getPermission(): string
    {
        return $this->permission;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @return array|null
     */
    public function getSubMenus(): ?array
    {
        return $this->subMenus;
    }
}

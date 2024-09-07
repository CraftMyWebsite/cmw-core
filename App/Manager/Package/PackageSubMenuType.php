<?php

namespace CMW\Manager\Package;

class PackageSubMenuType
{
    private string $title;
    private string $permission;
    private string $url;

    /**
     * @param string $title
     * @param string $permission
     * @param string $url
     */
    public function __construct(string $title, string $permission, string $url)
    {
        $this->title = $title;
        $this->permission = $permission;
        $this->url = $url;
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
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }
}

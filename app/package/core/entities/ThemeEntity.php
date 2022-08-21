<?php

/* GET THE THEME CONFIGURATION ENTITY */

namespace CMW\Entity\Core;

class ThemeEntity
{

    public string $name;
    public string $author;
    public string $version;
    public string $cmwVersion;
    public ?array $packages;

    /**
     * @param string $name
     * @param string $author
     * @param string $version
     * @param string $cmwVersion
     * @param array|null $packages
     */
    public function __construct(string $name, string $author, string $version, string $cmwVersion, ?array $packages)
    {
        $this->name = $name;
        $this->author = $author;
        $this->version = $version;
        $this->cmwVersion = $cmwVersion;
        $this->packages = $packages;
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
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getCmwVersion(): string
    {
        return $this->cmwVersion;
    }

    /**
     * @return array|null
     */
    public function getPackages(): ?array
    {
        return $this->packages;
    }

}
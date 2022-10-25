<?php

/* GET THE THEME CONFIGURATION ENTITY */

namespace CMW\Entity\Core;

class ThemeEntity
{

    private string $name;
    private ?string $author;
    private ?array $authors;
    private string $version;
    private string $cmwVersion;
    private ?array $packages;

    /**
     * @param string $name
     * @param string|null $author
     * @param array|null $authors
     * @param string $version
     * @param string $cmwVersion
     * @param array|null $packages
     */
    public function __construct(string $name, ?string $author, ?array $authors, string $version, string $cmwVersion, ?array $packages)
    {
        $this->name = $name;
        $this->author = $author;
        $this->authors = $authors;
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
     * @return string|null
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }


    /**
     * @return array|null
     */
    public function getAuthors(): ?array
    {
        return $this->authors;
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
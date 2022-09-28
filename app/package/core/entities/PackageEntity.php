<?php

/* GET THE PACKAGE CONFIGURATION ENTITY */

namespace CMW\Entity\Core;

class PackageEntity
{
    private string $name;
    private string $description;
    private string $version;
    private string $author;
    //todo menus

    /**
     * @param string $name
     * @param string $description
     * @param string $version
     * @param string $author
     */
    public function __construct(string $name, string $description, string $version, string $author)
    {
        $this->name = $name;
        $this->description = $description;
        $this->version = $version;
        $this->author = $author;
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
    public function getAuthor(): string
    {
        return $this->author;
    }
}

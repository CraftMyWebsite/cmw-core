<?php

/* GET THE PACKAGE CONFIGURATION ENTITY */

namespace CMW\Entity\Core;

use CMW\Manager\Env\EnvManager;

class PackageEntity
{
    private string $name;
    private array $descriptions;
    private string $version;
    private string $author;
    /** @var \CMW\Entity\Core\PackageMenusEntity|\CMW\Entity\Core\PackageMenusEntity[] $menus */
    private array $menus;
    private bool $isGame;
    private bool $isCore;

    /**
     * @param string $name
     * @param array $descriptions
     * @param string $version
     * @param string $author
     * @param null|\CMW\Entity\Core\PackageMenusEntity[] $menus
     * @param bool $isGame
     * @param bool $isCore
     */
    public function __construct(string $name, array $descriptions, string $version, string $author, ?array $menus, bool $isGame, bool $isCore)
    {
        $this->name = $name;
        $this->descriptions = $descriptions;
        $this->version = $version;
        $this->author = $author;
        $this->menus = $menus;
        $this->isGame = $isGame;
        $this->isCore = $isCore;
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
        return $this->descriptions[EnvManager::getInstance()->getValue('LOCALE')];
    }

    /**
     * @return array
     */
    public function getDescriptions(): array
    {
        return $this->descriptions;
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

    /**
     * @return bool
     */
    public function isGame(): bool
    {
        return $this->isGame;
    }

    /**
     * @return bool
     */
    public function isCore(): bool
    {
        return $this->isCore;
    }

    /**
     * @return \CMW\Entity\Core\PackageMenusEntity[]
     */
    public function getMenus(): array
    {
        return $this->menus;
    }


}

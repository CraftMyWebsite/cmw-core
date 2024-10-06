<?php

namespace CMW\Entity\Core;

use CMW\Utils\Date;
use CMW\Entity\Users\UserEntity;

class ConditionEntity
{
    private int $id;
    private string $content;
    private bool $state;
    private string $update;
    private ?UserEntity $lastEditor;

    /**
     * @param int $id
     * @param string $content
     * @param bool $state
     * @param string $update
     * @param ?\CMW\Entity\Users\UserEntity $lastEditor
     */
    public function __construct(int $id, string $content, bool $state, string $update, ?UserEntity $lastEditor)
    {
        $this->id = $id;
        $this->content = $content;
        $this->state = $state;
        $this->update = $update;
        $this->lastEditor = $lastEditor;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return bool
     */
    public function isState(): bool
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getUpdate(): string
    {
        return $this->update;
    }

    /**
     * @return string
     */
    public function getUpdateFormatted(): string
    {
        return Date::formatDate($this->update);
    }

    /**
     * @return ?\CMW\Entity\Users\UserEntity
     */
    public function getLastEditor(): ?UserEntity
    {
        return $this->lastEditor;
    }
}

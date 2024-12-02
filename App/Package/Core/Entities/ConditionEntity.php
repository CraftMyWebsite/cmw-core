<?php

namespace CMW\Entity\Core;

use CMW\Entity\Users\UserEntity;
use CMW\Manager\Package\AbstractEntity;
use CMW\Utils\Date;

class ConditionEntity extends AbstractEntity
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
     * @param ?UserEntity $lastEditor
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
     * @return ?UserEntity
     */
    public function getLastEditor(): ?UserEntity
    {
        return $this->lastEditor;
    }
}

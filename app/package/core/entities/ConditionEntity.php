<?php

namespace CMW\Entity\Core;

use CMW\Entity\Users\UserEntity;

class ConditionEntity
{
    private int $conditionId;
    private string $conditionContent;
    private bool $conditionState;
    private string $conditionUpdate;
    private UserEntity $conditionAuthor;

    /**
     * @param int $conditionId
     * @param string $conditionContent
     * @param bool $conditionState
     * @param string $conditionUpdate
     * @param \CMW\Entity\Users\UserEntity $conditionAuthor
     */
    public function __construct(int $conditionId, string $conditionContent, bool $conditionState, string $conditionUpdate, UserEntity $conditionAuthor)
    {
        $this->conditionId = $conditionId;
        $this->conditionContent = $conditionContent;
        $this->conditionState = $conditionState;
        $this->conditionUpdate = $conditionUpdate;
        $this->conditionAuthor = $conditionAuthor;
    }

    /**
     * @return int
     */
    public function getConditionId(): int
    {
        return $this->conditionId;
    }

    /**
     * @return string
     */
    public function getConditionContent(): string
    {
        return $this->conditionContent;
    }

    /**
     * @return bool
     */
    public function isConditionState(): bool
    {
        return $this->conditionState;
    }

    /**
     * @return string
     */
    public function getConditionUpdate(): string
    {
        return $this->conditionUpdate;
    }

    /**
     * @return \CMW\Entity\Users\UserEntity
     */
    public function getConditionAuthor(): UserEntity
    {
        return $this->conditionAuthor;
    }
}
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

	public function __construct(int $conditionId, string $conditionContent,bool $conditionState, string $conditionUpdate, UserEntity $conditionAuthor)
	    {
	        $this->conditionId = $conditionId;
	        $this->conditionContent = $conditionContent;
	        $this->conditionState = $conditionState;
	        $this->conditionUpdate = $conditionUpdate;
	        $this->conditionAuthor = $conditionAuthor;
	    }

	public function getConditionId(): int
    {
        return $this->conditionId;
    }

    public function getConditionContent(): string
    {
        return $this->conditionContent;
    }

    public function getConditionState(): bool
    {
        return $this->conditionState;
    }

    public function getConditionUpdate(): string
    {
        return $this->conditionUpdate;
    }

    public function getConditionAuthor(): UserEntity
    {
        return $this->conditionAuthor;
    }

}
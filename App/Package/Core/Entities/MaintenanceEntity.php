<?php

namespace CMW\Entity\Core;

use CMW\Controller\Core\CoreController;

class MaintenanceEntity
{

    private bool $isEnable;
    private ?string $title;
    private ?string $description;
    private ?int $type;
    private ?string $targetDate;
    private string $lastUpdateDate;

    /**
     * @param bool $isEnable
     * @param string|null $title
     * @param string|null $description
     * @param int|null $type
     * @param string|null $targetDate
     * @param string $lastUpdateDate
     */
    public function __construct(bool $isEnable, ?string $title, ?string $description, ?int $type, ?string $targetDate, string $lastUpdateDate)
    {
        $this->isEnable = $isEnable;
        $this->title = $title;
        $this->description = $description;
        $this->type = $type;
        $this->targetDate = $targetDate;
        $this->lastUpdateDate = $lastUpdateDate;
    }

    /**
     * @return bool
     */
    public function isEnable(): bool
    {
        return $this->isEnable;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return int|null
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getTargetDate(): ?string
    {
        return $this->targetDate;
    }

    /**
     * @return string|null
     */
    public function getTargetDateFormatted(): ?string
    {
        return CoreController::formatDate($this->targetDate);
    }

    /**
     * @return string
     */
    public function getLastUpdateDate(): string
    {
        return $this->lastUpdateDate;
    }

    /**
     * @return string
     */
    public function getLastUpdateDateFormatted(): string
    {
        return CoreController::formatDate($this->lastUpdateDate);
    }
}
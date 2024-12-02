<?php

namespace CMW\Entity\Core;

use CMW\Manager\Package\AbstractEntity;
use CMW\Utils\Date;

class MaintenanceEntity extends AbstractEntity
{
    private bool $isEnable;
    private bool $noEnd;
    private ?string $title;
    private ?string $description;
    private ?int $type;
    private ?string $targetDate;
    private string $lastUpdateDate;
    private bool $isOverrideTheme;
    private ?string $overrideThemeCode;

    /**
     * @param bool $isEnable
     * @param bool $noEnd
     * @param string|null $title
     * @param string|null $description
     * @param int|null $type
     * @param string|null $targetDate
     * @param string $lastUpdateDate
     * @param bool $isOverrideTheme
     * @param string|null $overrideThemeCode
     */
    public function __construct(bool $isEnable, bool $noEnd, ?string $title, ?string $description, ?int $type, ?string $targetDate, string $lastUpdateDate, bool $isOverrideTheme, ?string $overrideThemeCode)
    {
        $this->isEnable = $isEnable;
        $this->noEnd = $noEnd;
        $this->title = $title;
        $this->description = $description;
        $this->type = $type;
        $this->targetDate = $targetDate;
        $this->lastUpdateDate = $lastUpdateDate;
        $this->isOverrideTheme = $isOverrideTheme;
        $this->overrideThemeCode = $overrideThemeCode;
    }

    /**
     * @return bool
     */
    public function isEnable(): bool
    {
        return $this->isEnable;
    }

    /**
     * @return bool
     */
    public function noEnd(): bool
    {
        return $this->noEnd;
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
        if (is_null($this->targetDate)) {
            return "undefined"; //TODO: Translate
        }

        return Date::formatDate($this->targetDate);
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
        return Date::formatDate($this->lastUpdateDate);
    }

    /**
     * @return bool
     */
    public function isOverrideTheme(): bool
    {
        return $this->isOverrideTheme;
    }

    /**
     * @return string|null
     */
    public function getOverrideThemeCode(): ?string
    {
        return $this->overrideThemeCode;
    }
}

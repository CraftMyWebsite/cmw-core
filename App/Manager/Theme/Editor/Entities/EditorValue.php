<?php

namespace CMW\Manager\Theme\Editor\Entities;

use CMW\Manager\Package\AbstractEntity;
use CMW\Manager\Package\EntityType;
use JetBrains\PhpStorm\ExpectedValues;

class EditorValue extends AbstractEntity
{
    public string $title;
    public string $themeKey;
    public mixed $defaultValue;
    public string $type;
    public array $selectOptions;
    public array $rangeOptions;

    /**
     * @param string $title
     * @param string $themeKey
     * @param mixed $defaultValue
     * @param string $type
     * @param EditorSelectOptions[] $selectOptions
     * @param EditorRangeOptions[] $rangeOptions
     */
    public function __construct(string $title, string $themeKey, mixed $defaultValue, #[ExpectedValues(flagsFromClass: EditorType::class)] string $type, #[EntityType(EditorSelectOptions::class)] array $selectOptions = [], #[EntityType(EditorRangeOptions::class)] array $rangeOptions = [])
    {
        $this->title = $title;
        $this->themeKey = $themeKey;
        $this->defaultValue = $defaultValue;
        $this->type = $type;
        $this->selectOptions = $selectOptions;
        $this->rangeOptions = $rangeOptions;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getThemeKey(): string
    {
        return $this->themeKey;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return EditorSelectOptions[]
     */
    public function getSelectOptions(): array
    {
        return $this->selectOptions;
    }

    /**
     * @return EditorRangeOptions[]
     */
    public function getRangeOptions(): array
    {
        return $this->rangeOptions;
    }
}

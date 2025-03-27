<?php

namespace CMW\Manager\Theme\Editor;

use JetBrains\PhpStorm\ExpectedValues;

class EditorValue
{
    public string $title;
    public string $themeKey;
    public mixed $defaultValue;
    public string $type;
    public ?array $selectOptions;

    /**
     * @param string $title
     * @param string $themeKey
     * @param mixed $defaultValue
     * @param string $type
     * @param EditorSelectOptions[]|null $selectOptions
     */
    public function __construct(string $title, string $themeKey, mixed $defaultValue, #[ExpectedValues(flagsFromClass: EditorType::class)] string $type, array $selectOptions = [])
    {
        $this->title = $title;
        $this->themeKey = $themeKey;
        $this->defaultValue = $defaultValue;
        $this->type = $type;
        $this->selectOptions = $selectOptions;
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
     * @return EditorSelectOptions[]|null
     */
    public function getSelectOptions(): ?array
    {
        return $this->selectOptions;
    }


}

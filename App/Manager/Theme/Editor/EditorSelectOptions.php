<?php

namespace CMW\Manager\Theme\Editor;

use CMW\Manager\Package\AbstractEntity;

class EditorSelectOptions extends AbstractEntity
{
    public string $value;
    public string $text;

    /**
     * @param string $value
     * @param string $text
     */
    public function __construct(string $value, string $text)
    {
        $this->value = $value;
        $this->text = $text;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getText(): string
    {
        return $this->text;
    }
}

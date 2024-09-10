<?php

namespace CMW\Manager\Components\Base;

use CMW\Manager\Components\IComponent;
use JetBrains\PhpStorm\ExpectedValues;

class InputComponentBase extends IComponent
{
    private string $placeholder = "Enter text";
    private string $type = "text";
    private string $name = "undefined";
    private bool $isRequired = true;
    private bool $isReadOnly = false;
    private bool $isDisabled = false;

    /**
     * @param string $placeholder
     * @return InputComponentBase
     */
    public function setPlaceholder(string $placeholder): InputComponentBase
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * @param string $type
     * @return InputComponentBase
     */
    public function setType(#[ExpectedValues(['button', 'checkbox', 'color', 'date', 'datetime-local', 'email', 'file', 'hidden', 'image',
        'month', 'number', 'password', 'radio', 'range', 'reset', 'search', 'submit', 'tel', 'text', 'time', 'url',
        'week'])] string $type): InputComponentBase
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param string $name
     * @return InputComponentBase
     */
    public function setName(string $name): InputComponentBase
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param bool $isRequired
     * @return InputComponentBase
     */
    public function setIsRequired(bool $isRequired): InputComponentBase
    {
        $this->isRequired = $isRequired;
        return $this;
    }

    /**
     * @return string
     */
    private function showRequired(): string
    {
        return $this->isRequired ? 'required' : '';
    }

    /**
     * @param bool $isReadOnly
     * @return InputComponentBase
     */
    public function setIsReadOnly(bool $isReadOnly): InputComponentBase
    {
        $this->isReadOnly = $isReadOnly;
        return $this;
    }

    /**
     * @return string
     */
    private function showReadOnly(): string
    {
        return $this->isReadOnly ? 'readonly' : '';
    }

    /**
     * @param bool $isDisabled
     * @return InputComponentBase
     */
    public function setIsDisabled(bool $isDisabled): InputComponentBase
    {
        $this->isDisabled = $isDisabled;
        return $this;
    }

    /**
     * @return string
     */
    private function showDisabled(): string
    {
        return $this->isDisabled ? 'disabled' : '';
    }

    /**
     * @inheritDoc
     */
    public function render(): void
    {
        print "<input   {$this->showId()}
                        type='$this->type' 
                        class='$this->classes'
                        name='$this->name'
                        placeholder='$this->placeholder'
                        {$this->showRequired()}
                        {$this->showReadOnly()}
                        {$this->showDisabled()}>";
    }
}
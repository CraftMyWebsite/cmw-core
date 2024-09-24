<?php

namespace CMW\Manager\Components\Base;

use CMW\Manager\Components\IComponent;

class TextareaComponentBase extends IComponent 
{
    private string $placeholder = "Enter text";
    private string $name = "undefined";
    private bool $isRequired = true;
    private bool $isReadOnly = false;
    private bool $isDisabled = false;
    private int $minLength = 0;
    private int $maxLength = 1000;

    private bool $spellcheck = true;

    public function setPlaceholder(string $placeholder): static {
        $this->placeholder = $placeholder;
        return $this;
    }

    public function setName(string $name): static {
        $this->name = $name;
        return $this;
    }

    public function setClasses(string $classes): static {
        $this->classes .= ' ' . $classes;
        return $this;
    }

    public function setIsRequired(bool $isRequired): static {
        $this->isRequired = $isRequired;
        return $this;
    }

    public function setIsReadOnly(bool $isReadOnly): static {
        $this->isReadOnly = $isReadOnly;
        return $this;
    }

    public function setIsDisabled(bool $isDisabled): static {
        $this->isDisabled = $isDisabled;
        return $this;
    }

    public function setMinLength(int $minLength): static {
        $this->minLength = $minLength;
        return $this;
    }

    public function setMaxLength(int $maxLength): static {
        $this->maxLength = $maxLength;
        return $this;
    }

    public function setSpellcheck(bool $spellcheck): static {
        $this->spellcheck = $spellcheck;
        return $this;
    }

    public function showDisabled(): string {
        return $this->isDisabled ? 'disabled' : '';
    }

    public function showReadOnly(): string {
        return $this->isReadOnly ? 'readonly' : '';
    }

    public function showRequired(): string {
        return $this->isRequired ? 'required' : '';
    }

    public function render(): void
    {
        print "<textarea {$this->showId()}
                         class='$this->classes'
                         name='$this->name'
                         placeholder='$this->placeholder'
                         spellcheck='$this->spellcheck'
                         minlength='$this->minLength'
                         maxlength='$this->maxLength'
                         {$this->showDisabled()}
                         {$this->showReadOnly()}
                         {$this->showRequired()}></textarea>";
    }
}
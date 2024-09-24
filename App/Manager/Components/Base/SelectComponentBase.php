<?php

namespace CMW\Manager\Components\Base;

use CMW\Manager\Components\IComponent;

class SelectComponentBase extends IComponent
{
    private string $name = "undefined";
    private bool $isRequired = true;
    private bool $isDisabled = false;
    private array $options = [];
    private string $selected = "";
    private string $placeholder = "Select an option";

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function setIsRequired(bool $isRequired): static
    {
        $this->isRequired = $isRequired;
        return $this;
    }

    public function setIsDisabled(bool $isDisabled): static
    {
        $this->isDisabled = $isDisabled;
        return $this;
    }

    public function setOptions(array $options): static
    {
        $this->options = $options;
        return $this;
    }

    public function setSelected(string $selected): static
    {
        $this->selected = $selected;
        return $this;
    }

    public function setPlaceholder(string $placeholder): static
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    private function isSelected(string $key): string
    {
        return $key === $this->selected ? "selected" : "";
    }

    private function showRequired(): string
    {
        return $this->isRequired ? "required" : "";
    }

    private function showDisabled(): string
    {
        return $this->isDisabled ? "disabled" : "";
    }

    private function printPlaceholder(): void {
        if ($this->placeholder) {
            print "<option value='' disabled selected>{$this->placeholder}</option>";
        }
    }

    private function printOptions(): void
    {
        foreach ($this->options as $key => $value) {
            print "<option value='$key' {$this->isSelected($key)}>$value</option>";
        }
    }

    public function render(): void
    {
        print "<select {$this->showId()}
                      class='$this->classes'
                      name='$this->name'
                      {$this->showRequired()}
                      {$this->showDisabled()}>";
                      $this->printPlaceholder();
                      $this->printOptions();
                      print "</select>";
    }
}
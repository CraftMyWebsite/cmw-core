<?php

namespace CMW\Manager\Components\Base;

use CMW\Manager\Components\IComponent;

class SelectOptionComponentBase extends IComponent {
    private string $value = "undefined";
    private string $text = "undefined";
    private bool $isSelected = false;
    private bool $isDisabled = false;

    public function setValue(string $value): static {
        $this->value = $value;
        return $this;
    }

    public function setText(string $text): static {
        $this->text = $text;
        return $this;
    }

    public function setIsSelected(bool $isSelected): static {
        $this->isSelected = $isSelected;
        return $this;
    }

    private function isSelected(): string {
        return $this->isSelected ? "selected" : "";
    }

    public function setIsDisabled(bool $isDisabled): static {
        $this->isDisabled = $isDisabled;
        return $this;
    }

    private function isDisabled(): string {
        return $this->isDisabled ? "disabled" : "";
    }

    public function render(): void {
        print "<option value='$this->value' {$this->isSelected()} {$this->isDisabled()}>$this->text</option>";
    }

}
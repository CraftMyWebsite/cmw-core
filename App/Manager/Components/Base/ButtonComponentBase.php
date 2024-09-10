<?php

namespace CMW\Manager\Components\Base;

use CMW\Manager\Components\IComponent;
use JetBrains\PhpStorm\ExpectedValues;

class ButtonComponentBase extends IComponent
{
    private string $text = "Button";
    #[ExpectedValues(['button', 'submit', 'reset'])] private string $type = "submit";
    private bool $isDisabled = false;
    private string $onClick = "";

    /**
     * @param string $text
     * @return $this
     */
    public function setText(string $text): ButtonComponentBase
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(#[ExpectedValues(['button', 'submit', 'reset'])] string $type): ButtonComponentBase
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param bool $isDisabled
     * @return $this
     */
    public function setIsDisabled(bool $isDisabled): ButtonComponentBase
    {
        $this->isDisabled = $isDisabled;
        return $this;
    }

    /**
     * @param string $onClick
     * @return $this
     */
    public function setOnClick(string $onClick): ButtonComponentBase
    {
        $this->onClick = $onClick;
        return $this;
    }

    /**
     * @return string
     */
    private function showDisabled(): string
    {
        return $this->isDisabled ? 'disabled' : '';
    }

    public function render(): void
    {
        print "<button {$this->showId()} type='$this->type' class='$this->classes' {$this->showDisabled()} 
                        onclick='$this->onClick'>
                    $this->text
                </button>";
    }
}
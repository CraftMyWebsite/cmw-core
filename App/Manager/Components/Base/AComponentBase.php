<?php

namespace CMW\Manager\Components\Base;

use CMW\Manager\Components\IComponent;
use JetBrains\PhpStorm\ExpectedValues;

class AComponentBase extends IComponent
{
    private string $href = "#";
    private bool $isDisabled = false;
    private string $onClick = "";
    private string $target = "_self";
    /* @var IComponent[] $children */
    private array $children;

    /**
     * @param string $href
     * @return AComponentBase
     */
    public function setHref(string $href): AComponentBase
    {
        $this->href = $href;
        return $this;
    }

    /**
     * @param bool $isDisabled
     * @return AComponentBase
     */
    public function setIsDisabled(bool $isDisabled): AComponentBase
    {
        $this->isDisabled = $isDisabled;
        return $this;
    }

    /**
     * @param string $onClick
     * @return AComponentBase
     */
    public function setOnClick(string $onClick): AComponentBase
    {
        $this->onClick = $onClick;
        return $this;
    }

    /**
     * @param string $target
     * @return AComponentBase
     */
    public function setTarget(#[ExpectedValues(['_blank', '_parent', '_self', '_top'])] string $target): AComponentBase
    {
        $this->target = $target;
        return $this;
    }

    /**
     * @param array $children
     * @return AComponentBase
     */
    public function setChildren(array $children): AComponentBase
    {
        $this->children = $children;
        return $this;
    }

    /**
     * @return void
     */
    public function printChildren(): void
    {
        foreach ($this->children as $child) {
            $child->render();
        }
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
        print "<a {$this->showId()} class='$this->classes' onclick='$this->onClick' {$this->showDisabled()} 
                    href='$this->href' target='$this->target'>";
        $this->printChildren();
        print "</a>";
    }
}
<?php

namespace CMW\Manager\Components\Base;

use CMW\Manager\Components\IComponent;

class DivComponentBase extends IComponent
{
    private string $onClick = "";
    /* @var IComponent[] $children */
    private array $children;

    /**
     * @param string $onClick
     * @return DivComponentBase
     */
    public function setOnClick(string $onClick): DivComponentBase
    {
        $this->onClick = $onClick;
        return $this;
    }

    /**
     * @param array $children
     * @return DivComponentBase
     */
    public function setChildren(array $children): DivComponentBase
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

    public function render(): void
    {
        print "<div {$this->showId()} class='$this->classes' onclick='$this->onClick'>";
        $this->printChildren();
        print "</div>";
    }
}
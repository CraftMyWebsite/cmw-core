<?php

namespace CMW\Manager\Components\Base;

use CMW\Manager\Components\IComponent;

class HeaderComponentBase extends IComponent
{
    /* @var IComponent[] $children */
    private array $children;

    /**
     * @param array $children
     * @return HeaderComponentBase
     */
    public function setChildren(array $children): HeaderComponentBase
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
        print "<header {$this->showId()} class='$this->classes'>";
        $this->printChildren();
        print "</header>";
    }
}
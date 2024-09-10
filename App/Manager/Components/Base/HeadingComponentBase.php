<?php

namespace CMW\Manager\Components\Base;

use CMW\Manager\Components\IComponent;
use JetBrains\PhpStorm\ExpectedValues;

class HeadingComponentBase extends IComponent
{

    private int $level = 1;
    private string $text = "";

    /**
     * @param int $level
     * @return HeadingComponentBase
     */
    public function setLevel(#[ExpectedValues([1, 2, 3, 4, 5, 6])] int $level): HeadingComponentBase
    {
        $this->level = $level;
        return $this;
    }

    /**
     * @param string $text
     * @return HeadingComponentBase
     */
    public function setText(string $text): HeadingComponentBase
    {
        $this->text = $text;
        return $this;
    }

    public function render(): void
    {
        print "<h$this->level {$this->showId()} class='$this->classes'>$this->text</h$this->level>";
    }
}
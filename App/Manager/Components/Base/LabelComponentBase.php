<?php

namespace CMW\Manager\Components\Base;

use CMW\Manager\Components\IComponent;

class LabelComponentBase extends IComponent
{
    private string $for = "";
    private string $text = "Input:";

    /**
     * @param string $for
     * @return LabelComponentBase
     */
    public function setFor(string $for): LabelComponentBase
    {
        $this->for = $for;
        return $this;
    }

    /**
     * @param string $text
     * @return LabelComponentBase
     */
    public function setText(string $text): LabelComponentBase
    {
        $this->text = $text;
        return $this;
    }


    /**
     * @inheritDoc
     */
    public function render(): void
    {
        print "<label {$this->showId()}
                      class='$this->classes'
                      for='$this->for'>
                      $this->text
               </label>";
    }
}
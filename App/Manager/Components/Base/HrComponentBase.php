<?php

namespace CMW\Manager\Components\Base;

use CMW\Manager\Components\IComponent;

class HrComponentBase extends IComponent
{
    public function render(): void
    {
       print "<hr {$this->showId()} class='$this->classes'>";
    }
}
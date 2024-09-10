<?php

namespace CMW\Theme\Sampler\Elements\Hr;

use CMW\Manager\Components\Base\HrComponentBase;

class SeparatorComponent extends HrComponentBase
{
    public function render(): void
    {
        HrComponentBase::create()
            ->setClasses("divider")
            ->render();
    }
}
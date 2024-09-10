<?php

namespace CMW\Theme\Sampler\Elements\Containers;

use CMW\Manager\Components\Base\DivComponentBase;
use CMW\Theme\Sampler\Elements\Heading\TitleComponent;
use CMW\Theme\Sampler\Elements\Hr\SeparatorComponent;

class TitleContainer extends DivComponentBase
{
    public function render(): void
    {
        DivComponentBase::create()
            ->setClasses('col-lg-8 align-self-end')
            ->setChildren([
                TitleComponent::create(),
                SeparatorComponent::create(),
            ])
            ->render();
    }

}
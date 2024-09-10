<?php

namespace CMW\Theme\Sampler\Elements\Containers;

use CMW\Manager\Components\Base\DivComponentBase;

class HeroWrapperComponent extends DivComponentBase
{
    public function render(): void
    {
        DivComponentBase::create()
            ->setClasses('row gx-4 gx-lg-5 h-100 align-items-center justify-content-center text-center')
            ->setChildren([
                TitleContainer::create(),
            ])
            ->render();
    }
}
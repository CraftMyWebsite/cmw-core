<?php

namespace CMW\Theme\Sampler\Elements\Containers;

use CMW\Manager\Components\Base\DivComponentBase;
use CMW\Manager\Components\Base\HeaderComponentBase;

class HeaderContainerComponent extends HeaderComponentBase
{
    public function render(): void
    {
        HeaderComponentBase::create()
            ->setClasses("masthead")
            ->setChildren([
                DivComponentBase::create()->setClasses('container px-4 px-lg-5 h-100')
                    ->setChildren([

                ])
            ])
            ->render();
    }

}
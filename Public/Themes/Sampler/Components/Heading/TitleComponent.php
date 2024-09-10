<?php

namespace CMW\Theme\Sampler\Elements\Heading;


use CMW\Manager\Components\Base\HeadingComponentBase;

class TitleComponent extends HeadingComponentBase
{
    private string $text = "CraftMyWebsite";

    public function setText(string $text): static
    {
        $this->text = $text;
        return $this;
    }

    public function render(): void
    {
        HeadingComponentBase::create()
            ->setLevel(1)
            ->setClasses("text-white font-weight-bold")
            ->setText($this->text)
            ->render();
    }
}
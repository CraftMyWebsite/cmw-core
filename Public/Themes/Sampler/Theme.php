<?php

namespace CMW\Theme\Sampler;

use CMW\Manager\Theme\IThemeConfig;

class Theme implements IThemeConfig
{
    public function name(): string
    {
        return "Sampler";
    }

    public function version(): string
    {
        return "0.0.1";
    }

    public function cmwVersion(): string
    {
        return "2.0";
    }

    public function author(): ?string
    {
        return "CraftMyWebsite";
    }

    public function authors(): array
    {
        return [];
    }

    public function compatiblesPackages(): array
    {
        return [
            "Core", "Pages", "Users", "Faq", "News", "Votes", "Wiki",
        ];
    }

    public function requiredPackages(): array
    {
        return ["Core", "Users"];
    }
}
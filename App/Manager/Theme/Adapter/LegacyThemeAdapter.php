<?php

namespace CMW\Manager\Theme\Adapter;

use CMW\Manager\Theme\IThemeConfig;
use CMW\Manager\Theme\IThemeConfigV2;

final class LegacyThemeAdapter implements IThemeConfigV2
{
    public function __construct(private IThemeConfig $v1) {}

    public function name(): string               { return $this->v1->name(); }
    public function version(): string            { return $this->v1->version(); }
    public function cmwVersion(): string         { return $this->v1->cmwVersion(); }
    public function imageLink(): ?string         { return $this->v1->imageLink(); }
    public function authors(): array             { return $this->v1->authors(); }
    public function compatiblesPackages(): array    { return $this->v1->compatiblesPackages(); }
    public function requiredPackages(): array    { return $this->v1->requiredPackages(); }
}
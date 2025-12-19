<?php

namespace CMW\Manager\Package\Adapter;

use CMW\Manager\Package\IPackageConfig;
use CMW\Manager\Package\IPackageConfigV2;
use CMW\Manager\Updater\UpdatesManager;

final class LegacyPackageAdapter implements IPackageConfigV2
{
    public function __construct(private IPackageConfig $v1) {}

    public function name(): string               { return $this->v1->name(); }
    public function version(): string            { return $this->v1->version(); }

    public function cmwVersion(): string         { return UpdatesManager::getCmwLatest(); }
    public function imageLink(): ?string         { return null; }
    public function authors(): array             { return $this->v1->authors(); }
    public function isGame(): bool               { return $this->v1->isGame(); }
    public function isCore(): bool               { return $this->v1->isCore(); }
    public function menus(): ?array              { return $this->v1->menus(); }
    public function compatiblesPackages(): array { return []; }
    public function requiredPackages(): array    { return $this->v1->requiredPackages(); }
    public function uninstall(): bool            { return $this->v1->uninstall(); }
}

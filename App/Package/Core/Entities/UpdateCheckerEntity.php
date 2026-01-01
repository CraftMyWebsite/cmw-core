<?php

namespace CMW\Entity\Core;

use CMW\Manager\Package\AbstractEntity;
use CMW\Utils\Date;

/**
 * Class: @UpdateCheckerEntity
 * @package Core
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/entities
 */
class UpdateCheckerEntity extends AbstractEntity
{
    private string $name;
    private ?string $marketName;
    private string $type; // 'theme' | 'package'
    private string $localVersion;
    private string $remoteVersion;
    private ?string $dateRelease;
    private ?int $versionId;

    public function __construct(
        string $name,
        ?string $marketName,
        string $type,
        string $localVersion,
        string $remoteVersion,
        ?string $dateRelease = null,
        ?int $versionId = null
    ) {
        $this->name = $name;
        $this->marketName = $marketName;
        $this->type = $type;
        $this->localVersion = $localVersion;
        $this->remoteVersion = $remoteVersion;
        $this->dateRelease = $dateRelease;
        $this->versionId = $versionId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function marketName(): ?string
    {
        return $this->marketName;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function localVersion(): string
    {
        return $this->localVersion;
    }

    public function remoteVersion(): string
    {
        return $this->remoteVersion;
    }

    public function dateRelease(): ?string
    {
        return Date::formatDate($this->dateRelease);
    }

    public function versionId(): ?int
    {
        return $this->versionId;
    }

    public function isOutdated(): bool
    {
        return $this->localVersion !== $this->remoteVersion;
    }
}

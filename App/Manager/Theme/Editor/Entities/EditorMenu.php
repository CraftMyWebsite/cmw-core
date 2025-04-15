<?php

namespace CMW\Manager\Theme\Editor\Entities;

use CMW\Manager\Package\AbstractEntity;
use CMW\Manager\Package\EntityType;

class EditorMenu extends AbstractEntity
{
    public string $title;
    public string $key;
    public ?string $scope;
    public ?string $requiredPackage;

    public array $values;

    /**
     * @param string $title
     * @param string $key
     * @param string|null $scope
     * @param string|null $requiredPackage
     * @param EditorValue[] $values
     */
    public function __construct(string $title, string $key, ?string $scope, ?string $requiredPackage, #[EntityType(EditorValue::class)] array $values)
    {
        $this->title = $title;
        $this->key = $key;
        $this->scope = $scope;
        $this->requiredPackage = $requiredPackage;
        $this->values = $values;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getMenuKey(): string
    {
        return $this->key;
    }

    /**
     * @return string|null
     */
    public function getScope(): ?string
    {
        return $this->scope;
    }

    /**
     * @return string|null
     */
    public function getRequiredPackage(): ?string
    {
        return $this->requiredPackage;
    }

    /**
     * @return \CMW\Manager\Theme\Editor\Entities\EditorValue[]
     */
    public function getValues(): array
    {
        return $this->values;
    }
}

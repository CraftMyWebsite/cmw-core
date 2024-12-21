<?php

namespace CMW\Entity\Users\Settings;

use CMW\Manager\Package\AbstractEntity;

/**
 * Class: @BulkSettingsEntity
 * @package Users
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/entities
 */
class BulkSettingsEntity extends AbstractEntity
{
    private string $name;
    private string $value;

    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}

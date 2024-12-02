<?php

namespace CMW\Entity\Users;

use CMW\Manager\Package\AbstractEntity;
use CMW\Utils\Date;

class BlacklistedPseudoEntity extends AbstractEntity
{
    private int $id;
    private string $pseudo;
    private string $dateBlacklisted;

    /**
     * @param int $id
     * @param string $pseudo
     * @param string $dateBlacklisted
     */
    public function __construct(int $id, string $pseudo, string $dateBlacklisted)
    {
        $this->id = $id;
        $this->pseudo = $pseudo;
        $this->dateBlacklisted = $dateBlacklisted;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    /**
     * @return string
     */
    public function getDateBlacklisted(): string
    {
        return $this->dateBlacklisted;
    }

    /**
     * @return string
     */
    public function getDateBlacklistedFormatted(): string
    {
        return Date::formatDate($this->dateBlacklisted);
    }
}

<?php

namespace CMW\Entity\Users;

use CMW\Controller\Core\CoreController;

class BlacklistedPseudoEntity
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
        return CoreController::formatDate($this->dateBlacklisted);
    }
}

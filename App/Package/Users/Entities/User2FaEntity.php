<?php

namespace CMW\Entity\Users;

use CMW\Manager\Security\EncryptManager;
use CMW\Manager\Twofa\TwoFaManager;

class User2FaEntity
{
    private int $userId;
    private bool $isEnabled;
    private string $secret;
    private bool $isEnforced;

    /**
     * @param int $userId
     * @param bool $isEnabled
     * @param string $secret
     * @param bool $isEnforced
     */
    public function __construct(int $userId, bool $isEnabled, string $secret, bool $isEnforced)
    {
        $this->userId = $userId;
        $this->isEnabled = $isEnabled;
        $this->secret = $secret;
        $this->isEnforced = $isEnforced;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @return string
     * @desc Return decrypted secret.
     */
    public function get2FaSecretDecoded(): string
    {
        return EncryptManager::decrypt($this->secret);
    }


    /**
     * @param int $size
     * @return string
     */
    public function getQrCode(int $size): string
    {
        return (new TwoFaManager())->getQrCode(EncryptManager::decrypt($this->secret), $size);
    }

    /**
     * @return bool
     */
    public function isEnforced(): bool
    {
        return $this->isEnforced;
    }
}
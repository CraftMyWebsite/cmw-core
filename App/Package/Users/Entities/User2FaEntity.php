<?php

namespace CMW\Entity\Users;

use CMW\Manager\Security\EncryptManager;
use CMW\Manager\TwoFaManager\TwoFaManager;

class User2FaEntity
{
    private int $userId;
    private bool $isEnabled;
    private string $secret;

    /**
     * @param int $userId
     * @param bool $isEnabled
     * @param string $secret
     */
    public function __construct(int $userId, bool $isEnabled, string $secret)
    {
        $this->userId = $userId;
        $this->isEnabled = $isEnabled;
        $this->secret = $secret;
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
}
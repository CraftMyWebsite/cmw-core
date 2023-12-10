<?php

namespace CMW\Manager\Security;

use CMW\Manager\Env\EnvManager;

class EncryptManager
{

    private static int $ivLength = 16;

    public static function getSalt(): string
    {
        return EnvManager::getInstance()->getValue('SALT');
    }

    public static function getSaltPass(): string
    {
        return EnvManager::getInstance()->getValue('SALT_PASS');
    }

    public static function getSaltIv(): string
    {
        return hex2bin(EnvManager::getInstance()->getValue('SALT_IV'));
    }

    /**
     * @param string $data
     * @return string
     * @desc Hash data with your unique salt
     */
    public static function encrypt(string $data): string
    {
        $encrypted = @openssl_encrypt($data, 'AES-256-CBC', self::getSaltPass(), OPENSSL_RAW_DATA, self::getSaltIv());

        $encrypted .= self::getSalt();

        return base64_encode($encrypted);
    }

    /**
     * @param string $hashedData
     * @return string
     * @desc Convert hashed data, to plain data
     */
    public static function decrypt(string $hashedData): string
    {
        $encryptedData = base64_decode($hashedData);

        $saltLength = strlen(self::getSalt());
        $encryptedData = substr($encryptedData, 0, -$saltLength);

        return openssl_decrypt($encryptedData, 'AES-256-CBC', self::getSaltPass(), OPENSSL_RAW_DATA, self::getSaltIv());
    }

    /**
     * @param string $plainData
     * @param string $encryptedData
     * @return bool
     */
    public static function isValueEquals(string $plainData, string $encryptedData): bool
    {
        return hash_equals(self::encrypt($plainData), $encryptedData);
    }
}
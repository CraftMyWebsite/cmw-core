<?php

namespace CMW\Manager\Twofa;

use CMW\Manager\Env\EnvManager;
use CMW\Utils\Website;
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\TwoFactorAuthException;

class TwoFaManager
{
    private TwoFactorAuth $_instance;

    public function __construct()
    {
        require_once EnvManager::getInstance()->getValue('DIR') . 'App/Manager/Twofa/Vendors/twofactorauth/lib/TwoFactorAuth.php';
        require_once EnvManager::getInstance()->getValue('DIR') . 'App/Manager/Twofa/Vendors/twofactorauth/lib/Algorithm.php';
        require_once EnvManager::getInstance()->getValue('DIR') . 'App/Manager/Twofa/Vendors/twofactorauth/lib/TwoFactorAuthException.php';

        require_once EnvManager::getInstance()->getValue('DIR') . 'App/Manager/Twofa/Vendors/twofactorauth/lib/Providers/Rng/IRNGProvider.php';
        require_once EnvManager::getInstance()->getValue('DIR') . 'App/Manager/Twofa/Vendors/twofactorauth/lib/Providers/Rng/CSRNGProvider.php';
        require_once EnvManager::getInstance()->getValue('DIR') . 'App/Manager/Twofa/Vendors/twofactorauth/lib/Providers/Time/ITimeProvider.php';
        require_once EnvManager::getInstance()->getValue('DIR') . 'App/Manager/Twofa/Vendors/twofactorauth/lib/Providers/Time/LocalMachineTimeProvider.php';
        require_once EnvManager::getInstance()->getValue('DIR') . 'App/Manager/Twofa/Vendors/twofactorauth/lib/Providers/Qr/IQRCodeProvider.php';
        require_once EnvManager::getInstance()->getValue('DIR') . 'App/Manager/Twofa/Vendors/twofactorauth/lib/Providers/Qr/BaseHTTPQRCodeProvider.php';
        require_once EnvManager::getInstance()->getValue('DIR') . 'App/Manager/Twofa/Vendors/twofactorauth/lib/Providers/Qr/QRServerProvider.php';

        $this->_instance = new TwoFactorAuth();
    }

    /**
     * @return \RobThree\Auth\TwoFactorAuth
     * @desc Get @TwoFactorAuth instance for custom uses.
     */
    public function getInstance(): TwoFactorAuth
    {
        return $this->_instance;
    }

    /**
     * @return string|false
     * @desc Create a new 2FA secret
     */
    public function generateSecret(): string|false
    {
        try {
            return $this->_instance->createSecret();
        } catch (TwoFactorAuthException $e) {
            return false;
        }
    }

    /**
     * @param string $secret
     * @param string $submittedSecret
     * @return bool
     * @desc Check if the submitted secret is correct.
     */
    public function isSecretValid(string $secret, string $submittedSecret): bool
    {
        return $this->_instance->verifyCode($secret, $submittedSecret);
    }

    /**
     * @param string $secret
     * @param int $size
     * @return string|false
     */
    public function getQrCode(string $secret, int $size): string|false
    {
        try {
            return $this->_instance->getQRCodeImageAsDataUri(Website::getWebsiteName(), $secret, $size);
        } catch (TwoFactorAuthException $e) {
            return false;
        }
    }

}

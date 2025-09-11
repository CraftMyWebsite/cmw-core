<?php

namespace CMW\Manager\Updater;

use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Env\EnvManager;

class UpdatesManager
{
    /**
     * @return bool
     * @desc Return if the website can test update
     */
    public static function isTestAPI(): bool
    {
        return (bool)EnvManager::getInstance()->getValue('TEST_API_UPDATE');
    }

    /**
     * @return bool
     * @desc Return if the website is in recette env
     */
    public static function isRecette(): bool
    {
        return (bool)EnvManager::getInstance()->getValue('IS_RECETTE');
    }

    /**
     * @return string
     * @desc Return the local CMW version
     */
    public static function getVersion(): string
    {
        return EnvManager::getInstance()->getValue('VERSION') ?? 'DEV';
    }

    /**
     * @return \stdClass|null
     * @desc Return the latest CMW version. If we can't reach API, we return NULL
     * @todo Cache this data
     */
    public static function getCmwLatest(): mixed
    {
        if (self::isTestAPI()) {
            return PublicAPI::getData('cms/latest/test');
        }
        return PublicAPI::getData('cms/latest');
    }

    /**
     * @return bool
     * @desc Return true if a new version is available.
     */
    public static function checkNewUpdateAvailable(): bool
    {
        $latest = self::getCmwLatest()['value'];
        return $latest !== null && self::getVersion() !== $latest && !self::ignoreUpdates();
    }

    /**
     * @return bool
     * @desc Check if the website ignore check updates.
     * <p>To turn off the update checker, you need to
     * set the ENV vars "UPDATE_CHECKER" to 0 and "DEVMODE" to 1</p>
     */
    public static function ignoreUpdates(): bool
    {
        return EnvManager::getInstance()->getValue('UPDATE_CHECKER') === '0' &&
            EnvManager::getInstance()->getValue('DEVMODE') === '1';
    }
}

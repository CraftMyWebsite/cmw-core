<?php

namespace CMW\Manager\Updater;

use CMW\Manager\Api\PublicAPI;
use CMW\Utils\Utils;

class UpdatesManager
{

    /**
     * @return string
     * @desc Return the local CMW version
     */
    public static function getVersion(): string
    {
        return Utils::getEnv()->getValue("VERSION");
    }


    /**
     * @return string|null
     * @desc Return the latest CMW version. If we can't reach API, we return NULL
     */
    public static function getLatestVersion(): ?string
    {
        try {
            return json_decode(file_get_contents(PublicAPI::getUrl() . "/getCmwLatest"), false, 512, JSON_THROW_ON_ERROR)->version;
        } catch (\JsonException) {
        }

        return null;
    }


    /**
     * @return bool
     * @desc Return true if a new version is available.
     */
    public static function checkNewUpdateAvailable(): bool
    {
        return self::getLatestVersion() !== null && self::getVersion() !== self::getLatestVersion() && !self::ignoreUpdates();
    }

    /**
     * @return bool
     * @desc Check if the website ignore check updates.
     * <p>To turn off the update checker, you need to
     * set the ENV vars "UPDATE_CHECKER" to 0 and "DEVMODE" to 1</p>
     */
    public static function ignoreUpdates(): bool
    {
        return Utils::getEnv()->getValue("UPDATE_CHECKER") === "0" &&
            Utils::getEnv()->getValue("DEVMODE") === "1";
    }
}

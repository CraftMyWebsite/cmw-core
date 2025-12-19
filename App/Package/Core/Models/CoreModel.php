<?php

namespace CMW\Model\Core;

use CMW\Manager\Cache\SimpleCacheManager;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;

/**
 * Class: @coreController
 * @package Core
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 */
class CoreModel extends AbstractModel
{
    private const string CACHE_KEY = 'options';
    private const string CACHE_SUBFOLDER = 'Core';

    public function fetchOption(string $option): ?string
    {
        // Check cache
        if (SimpleCacheManager::checkCache(self::CACHE_KEY, self::CACHE_SUBFOLDER)) {
            $cachedOptions = SimpleCacheManager::getCache(self::CACHE_KEY, self::CACHE_SUBFOLDER);

            // Search in cached data
            if (\is_array($cachedOptions)) {
                foreach ($cachedOptions as $conf) {
                    if (($conf['option_name'] === $option) && isset($conf['option_value'])) {
                        return $conf['option_value'];
                    }
                }
            }
        }

        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT option_name, option_value FROM cmw_core_options');

        if (!$req->execute()) {
            return null;
        }

        $allOptions = $req->fetchAll();

        // Store in cache
        if (!empty($allOptions)) {
            SimpleCacheManager::storeCache($allOptions, self::CACHE_KEY, self::CACHE_SUBFOLDER);
        }

        // Find and return the requested option
        foreach ($allOptions as $conf) {
            if ($conf['option_name'] === $option) {
                return $conf['option_value'] ?? null;
            }
        }

        return null;
    }

    /**
     * @param string $option
     * @return string
     * @desc get the selected option
     */
    public static function getOptionValue(string $option): string
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT option_value FROM cmw_core_options WHERE option_name = ?');

        return ($req->execute([$option])) ? $req->fetch()['option_value'] : '';
    }

    public function fetchOptions(): array
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT * FROM cmw_core_options');

        if ($req->execute()) {
            return $req->fetchAll();
        }

        return ($req->execute()) ? $req->fetchAll() : [];
    }

    /**
     * @param string $optionName
     * @param string $optionValue
     * @return bool
     */
    public function updateOption(string $optionName, string $optionValue): bool
    {
        $sql = 'INSERT INTO cmw_core_options (option_name, option_value, option_updated)
                VALUES (:option_name, :option_value, NOW())
                ON DUPLICATE KEY UPDATE option_value=VALUES(option_value), option_updated=NOW()';
        $db = DatabaseManager::getInstance();

        $result = $db->prepare($sql)->execute(['option_name' => $optionName, 'option_value' => $optionValue]);

        //Update cache
        if ($result) {
            $this->updateOptionCacheValue($optionName, $optionValue);
        }

        return $result;
    }

    /**
     * @param string $optionName
     * @return bool
     */
    public function deleteOption(string $optionName): bool
    {
        $sql = 'DELETE FROM cmw_core_options WHERE option_name = :option_name';
        $db = DatabaseManager::getInstance();

        $result = $db->prepare($sql)->execute(['option_name' => $optionName]);

        //Update cache
        if ($result) {
            $this->updateOptionCacheValue($optionName, '');
        }

        return $result;
    }

    /**
     * <p>Update the option value in the cache only.</p>
     * @param string $optionName
     * @param string $optionValue
     * @return void
     */
    private function updateOptionCacheValue(string $optionName, string $optionValue): void
    {
        if (SimpleCacheManager::cacheExist(self::CACHE_KEY, self::CACHE_SUBFOLDER)) {
            $cachedOptions = SimpleCacheManager::getCache(self::CACHE_KEY, self::CACHE_SUBFOLDER);

            if (\is_array($cachedOptions)) {
                foreach ($cachedOptions as &$conf) {
                    if ($conf['option_name'] === $optionName) {
                        $conf['option_value'] = $optionValue;
                        SimpleCacheManager::storeCache($cachedOptions, self::CACHE_KEY, self::CACHE_SUBFOLDER);
                        return;
                    }
                }
                unset($conf);

                // If not found, add it
                $cachedOptions[] = ['option_name' => $optionName, 'option_value' => $optionValue];
                SimpleCacheManager::storeCache($cachedOptions, self::CACHE_KEY, self::CACHE_SUBFOLDER);
            }
        }
    }

    /**
     * <p>Clear the options cache</p>
     * @return void
     */
    public function clearOptionsCache(): void
    {
        if (SimpleCacheManager::cacheExist(self::CACHE_KEY, self::CACHE_SUBFOLDER)) {
            SimpleCacheManager::deleteSpecificCacheFile(self::CACHE_KEY, self::CACHE_SUBFOLDER);
        }
    }
}

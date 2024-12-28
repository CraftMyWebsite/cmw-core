<?php

namespace CMW\Manager\Package;

use CMW\Controller\Core\PackageController;
use function is_null;

/**
 * <p>Useful methods for packages</p>
 * @package CMW\Manager\Package
 */
class P
{
    private static mixed $callable;

    /**
     * <p>Check if the package exist and call the callable</p>
     * <p>Return null if the package doesn't exist and if we don't provide $orElse parameter</p>
     * @param string $package
     * @param callable $callable
     * @param string|null $orElse
     * @return self|null
     */
    public static function ifExist(string $package, callable $callable, ?string $orElse = null): ?self
    {
        $isInstalled = PackageController::getPackage($package) !== null;

        if (!$isInstalled) {
            if (!is_null($orElse)) {
                $fallbackCallable = static function () use ($orElse) {
                    return $orElse;
                };

                self::$callable = $fallbackCallable();

                return new self;
            }
            return null;
        }

        self::$callable = $callable();

        $callable();

        return new self;
    }

    /**
     * <p>Print the callable <b>THE CALLABLE NEED TO RETURN A PRINTABLE STATEMENT !!</b></p>
     * @return void
     */
    public static function p(): void
    {
        print self::$callable;
    }
}
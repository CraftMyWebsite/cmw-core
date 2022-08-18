<?php

namespace CMW\Manager\Permission;

use RuntimeException;

class PermissionManager
{

    public static function canCreateFile(string $path): bool
    {
        (new PermissionManager)->createDirectory($path); //Create the log directory
        return is_writable($path); //todo test-it
    }

    /**
     * @param string $path
     * @return void
     * @desc Create the directory to store the logs files
     */
    private  function createDirectory(string $path): void
    {
        if (!file_exists($path) && !mkdir($concurrentDirectory = $path) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
    }


}
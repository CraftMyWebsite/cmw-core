<?php

namespace CMW\Manager\Permission;

class PermissionManager
{

    public static function canCreateFile(string $path): bool
    {
        return is_writable($path); //todo test-it
    }

}
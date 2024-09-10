<?php

namespace CMW\Manager\Components;

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Manager\AbstractManager;
use CMW\Utils\Directory;
use function is_dir;

class ComponentsManager extends AbstractManager
{

    /**
     * @param string $themeName
     * @return void
     * @desc Load theme components
     */
    public function loadThemeComponents(string $themeName): void
    {
        $path = EnvManager::getInstance()->getValue('DIR') . 'Public/Themes/' . $themeName . '/Components';

        //If dir Elements doesn't exist, ignore.
        if (!is_dir($path)) {
            return;
        }

        $files = Directory::getFilesRecursively($path);

        foreach ($files as $file) {
            require_once $file;
        }
    }

}
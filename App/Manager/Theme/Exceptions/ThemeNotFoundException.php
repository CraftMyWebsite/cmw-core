<?php

namespace CMW\Manager\Theme\Exceptions;

use CMW\Controller\Core\SecurityController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use JetBrains\PhpStorm\NoReturn;
use Exception;
use Override;

class ThemeNotFoundException extends Exception
{
    private string $themeName;

    /**
     * @param string $themeName
     */
    public function __construct(string $themeName)
    {
        parent::__construct();
        $this->themeName = $themeName;
    }

    /**
     * @return string
     */
    #[Override]
    public function __toString(): string
    {
        return "Theme $this->themeName not found";
    }

    #[NoReturn]
    public function invokeErrorPage(): void
    {
        $pathUrl = EnvManager::getInstance()->getValue('PATH_URL');

        if (!UsersController::isAdminLogged()) {
            print <<<HTML
                <div>
                    <h1>Configuration error, please contact the website administrator.</h1>
                    <pre>Error: <b>Invalid theme name.</b></pre>
                </div>
                HTML;
            die();
        }

        print <<<HTML
            <div>
                <h1>Error - Theme Configuration</h1>
                <h2>Theme $this->themeName not found</h2>
                <p>Check if the theme is installed and the name is correct</p>
                <pre>Path: <b>Public/Themes/$this->themeName/</b></pre>

                <hr>
                <p>Use the default theme <b>Sampler</b> ?</p>
                <form method="post" action="{$pathUrl}cmw-admin/theme/force/reset">
            HTML;
        (new SecurityManager())->insertHiddenToken();
        SecurityController::getPublicData();
        print <<<HTML
                    <button type="submit" name="theme" value="Sampler">Use Sampler</button>
                </form>
            </div>
            HTML;
        die();
    }
}

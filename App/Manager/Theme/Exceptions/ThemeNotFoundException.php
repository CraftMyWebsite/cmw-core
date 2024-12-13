<?php

namespace CMW\Manager\Theme\Exceptions;

use CMW\Controller\Core\SecurityController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
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

        $errorUserTitle = LangManager::translate('core.themeNotFoundException.user.title');
        $errorUserMessage1 = LangManager::translate('core.themeNotFoundException.user.message1');
        $errorUserMessage2 = LangManager::translate('core.themeNotFoundException.user.error');

        $errorTitle = LangManager::translate('core.themeNotFoundException.title');
        $errorMessage1 = LangManager::translate('core.themeNotFoundException.message1');
        $errorMessage2 = LangManager::translate('core.themeNotFoundException.message2');
        $path = LangManager::translate('core.themeNotFoundException.path');
        $useSampler = LangManager::translate('core.themeNotFoundException.use_sampler');
        $button = LangManager::translate('core.themeNotFoundException.button');

        if (!UsersController::isAdminLogged()) {
            print <<<HTML
                <style>
                body {
                background: #da405c;
                color: white;
                font-family: Helvetica, Arial, sans-serif;
                margin: 0;
                padding: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                height: 100vh;
                text-align: center;
                }
                h1 {
                    font-size: 2.5em;
                    margin-bottom: 0.5em;
                }
                p {
                    font-size: 1.2em;
                    margin-bottom: 1em;
                }
                pre {
                    background: rgba(255, 255, 255, 0.1);
                    padding: 10px;
                    border-radius: 5px;
                    font-size: 1em;
                    color: #ffcccb;
                    margin-bottom: 1em;
                }
                hr {
                    border: 0;
                    border-top: 2px solid rgba(255, 255, 255, 0.5);
                    margin: 2em 0;
                    width: 100%;
                }
                .error-container {
                    max-width: 600px;
                    margin: 0 auto;
                }
                </style>
                <div class="error-container">
                    <h1>$errorUserTitle</h1>
                    <p>$errorUserMessage1</p>
                    <hr>
                    <pre>$errorUserMessage2</pre>
                </div>
                HTML;
            die();
        }

        print <<<HTML
            <style>
            body {
            background: #da405c;
            color: white;
            font-family: Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
            }
            h1 {
                font-size: 2.5em;
                margin-bottom: 0.5em;
            }
            h2 {
                font-size: 1.5em;
                margin-bottom: 1em;
            }
            p {
                font-size: 1.2em;
                margin-bottom: 1em;
            }
            pre {
                background: rgba(255, 255, 255, 0.1);
                padding: 10px;
                border-radius: 5px;
                font-size: 1em;
                color: #ffcccb;
                margin-bottom: 1em;
            }
            hr {
                border: 0;
                border-top: 2px solid rgba(255, 255, 255, 0.5);
                margin: 2em 0;
                width: 100%;
            }
            button {
                border: none;
                border-radius: 5px;
                background: #647ddf;
                color: white;
                padding: 10px 20px;
                font-size: 1em;
                cursor: pointer;
                transition: background 0.3s;
            }
            button:hover {
                background: #286cd3;
            }
            .error-container {
                max-width: 600px;
                margin: 0 auto;
            }
            form {
                margin: 0;
            }
            </style>
            <body>
            <div class="error-container">
            <h1>$errorTitle</h1>
            <h2>$this->themeName $errorMessage1</h2>
            <p>$errorMessage2</p>
            <pre>$path <b>Public/Themes/$this->themeName/</b></pre>
            <hr>
            <p>$useSampler</p>
            <form method="post" action="{$pathUrl}cmw-admin/theme/force/reset">
            HTML;
        SecurityManager::getInstance()->insertHiddenToken();
        SecurityController::getPublicData();
        print <<<HTML
                    <button type="submit" name="theme" value="Sampler">$button</button>
                    </form>
                </div>
            </body>
            HTML;
        die();
    }
}

<?php

namespace CMW\Manager\Views;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Components\ComponentsManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Router\RouterException;
use CMW\Manager\Theme\ThemeManager;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\ExpectedValues;
use function is_file;

class View
{
    private ?string $package;
    private ?string $viewFile;
    private ?string $customPath = null;
    private ?string $customTemplate = null;
    private array $includes;
    private array $variables;
    private bool $needAdminControl;
    private bool $isAdminFile;
    private string $themeName;

    /**
     * @param string|null $package
     * @param string|null $viewFile
     * @param bool|null $isAdminFile
     */
    public function __construct(?string $package = null, ?string $viewFile = null, ?bool $isAdminFile = false)
    {
        $this->package = $package;
        $this->viewFile = $viewFile;
        $this->includes = $this->generateInclude();
        $this->variables = [];
        $this->needAdminControl = false;
        $this->isAdminFile = $isAdminFile;
        $this->themeName = ThemeManager::getInstance()->getCurrentTheme()->name();
    }

    /**
     * @param string $package
     * @param string $viewFile
     * @return void
     * @throws \CMW\Manager\Router\RouterException
     */
    public static function basicPublicView(string $package, string $viewFile): void
    {
        $view = new self($package, $viewFile);
        $view->view();
    }

    /**
     * @param string $package
     * @param string $viewFile
     * @return \CMW\Manager\Views\View
     */
    public static function createPublicView(string $package, string $viewFile): View
    {
        return new self($package, $viewFile);
    }

    /**
     * @param string $package
     * @param string $viewFile
     * @return \CMW\Manager\Views\View
     */
    public static function createAdminView(string $package, string $viewFile): View
    {
        $view = new self($package, $viewFile);

        $view->setAdminView()->needAdminControl();

        return $view;
    }

    /**
     * @return array|array[]
     */
    #[ArrayShape(['styles' => 'array', 'scripts' => 'array', 'php' => 'array'])]
    private function generateInclude(): array
    {
        $array = ['styles' => [], 'scripts' => [], 'array' => []];

        $array['scripts']['before'] = [];
        $array['scripts']['after'] = [];

        $array['php']['before'] = [];
        $array['php']['after'] = [];

        return $array;
    }

    /**
     * @param string $position
     * @param string $fileName
     * @return void
     */
    private function addScript(#[ExpectedValues(['after', 'before'])] string $position, string $fileName): void
    {
        $this->includes['scripts'][$position][] = $fileName;
    }

    /**
     * @param string $position
     * @param string $fileName
     * @return void
     */
    private function addPhp(#[ExpectedValues(['after', 'before'])] string $position, string $fileName): void
    {
        $this->includes['php'][$position][] = $fileName;
    }

    /**
     * @param string $package
     * @return $this
     */
    public function setPackage(string $package): self
    {
        $this->package = $package;
        return $this;
    }

    /**
     * @param string $viewFile
     * @return $this
     */
    public function setViewFile(string $viewFile): self
    {
        $this->viewFile = $viewFile;
        return $this;
    }

    /**
     * @param bool $needAdminControl
     * @return $this
     */
    public function needAdminControl(bool $needAdminControl = true): self
    {
        $this->needAdminControl = $needAdminControl;
        return $this;
    }

    /**
     * @param bool $isAdminFile
     * @return $this
     */
    public function setAdminView(bool $isAdminFile = true): self
    {
        $this->isAdminFile = $isAdminFile;
        return $this;
    }

    /**
     * @param string $variableName
     * @param mixed $variable
     * @return $this
     */
    public function addVariable(string $variableName, mixed $variable): self
    {
        $this->variables[$variableName] ??= $variable;
        return $this;
    }

    /**
     * @param array $variableList
     * @return $this
     */
    public function addVariableList(array $variableList): self
    {
        foreach ($variableList as $key => $value) {
            $this->addVariable($key, $value);
        }

        return $this;
    }

    /**
     * @param string ...$script
     * @return $this
     */
    public function addScriptBefore(string ...$script): self
    {
        foreach ($script as $scriptFile) {
            $this->addScript('before', $scriptFile);
        }

        return $this;
    }

    /**
     * @param string ...$script
     * @return $this
     */
    public function addScriptAfter(string ...$script): self
    {
        foreach ($script as $scriptFile) {
            $this->addScript('after', $scriptFile);
        }

        return $this;
    }

    /**
     * @param string ...$php
     * @return $this
     */
    public function addPhpBefore(string ...$php): self
    {
        foreach ($php as $scriptFile) {
            $this->addPhp('before', $scriptFile);
        }

        return $this;
    }

    /**
     * @param string ...$php
     * @return $this
     */
    public function addPhpAfter(string ...$php): self
    {
        foreach ($php as $scriptFile) {
            $this->addPhp('after', $scriptFile);
        }

        return $this;
    }

    /**
     * @param string ...$style
     * @return $this
     */
    public function addStyle(string ...$style): self
    {
        foreach ($style as $styleFile) {
            $this->includes['styles'][] = $styleFile;
        }

        return $this;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setCustomPath(string $path): self
    {
        $this->customPath = $path;
        return $this;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setCustomTemplate(string $path): self
    {
        $this->customTemplate = $path;
        return $this;
    }

    /**
     * @return string
     */
    private function getViewPath(): string
    {
        if ($this->customPath !== null) {
            return $this->customPath;
        }

        if ($this->isAdminFile) {
            return "App/Package/$this->package/Views/$this->viewFile.admin.view.php";
        }

        $publicPath = "Public/Themes/$this->themeName/Views/$this->package/$this->viewFile.view.php";

        if (is_file($publicPath)) {
            return $publicPath;
        }

        return "App/Package/$this->package/Public/$this->viewFile.view.php";
    }

    /**
     * @return string
     */
    private function getTemplateFile(): string
    {
        if ($this->customTemplate !== null) {
            return $this->customTemplate;
        }

        return ($this->isAdminFile)
            ? EnvManager::getInstance()->getValue('PATH_ADMIN_VIEW') . 'template.php'
            : "Public/Themes/$this->themeName/Views/template.php";
    }

    /**
     * @param array $includes
     * @param string $fileType
     * @return void
     */
    private static function loadIncludeFile(array $includes, #[ExpectedValues(['beforeScript', 'afterScript', 'beforePhp', 'afterPhp', 'styles'])] string $fileType): void
    {
        if (!in_array($fileType, ['beforeScript', 'afterScript', 'beforePhp', 'afterPhp', 'styles'])) {
            return;
        }

        // STYLES
        if ($fileType === 'styles') {
            foreach ($includes['styles'] as $style) {
                $styleLink = EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . $style;
                echo <<<HTML
                        <link rel="stylesheet" href="$styleLink">
                    HTML;
            }
        }

        // SCRIPTS
        if (in_array($fileType, ['beforeScript', 'afterScript'])) {
            $arrayAccessJs = $fileType === 'beforeScript' ? 'before' : 'after';
            foreach ($includes['scripts'][$arrayAccessJs] as $script) {
                $scriptLink = EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . $script;
                echo <<<HTML
                        <script src="$scriptLink"></script>
                    HTML;
            }
        }

        // PHP
        if (in_array($fileType, ['beforePhp', 'afterPhp'])) {
            $arrayAccessPhp = $fileType === 'beforePhp' ? 'before' : 'after';
            foreach ($includes['php'][$arrayAccessPhp] as $php) {
                $phpLink = EnvManager::getInstance()->getValue('DIR') . $php;
                include_once $phpLink;
            }
        }
    }

    /**
     * @throws RouterException
     */
    public function loadFile(): string
    {
        $path = $this->getViewPath();

        if (!is_file($path)) {
            throw new RouterException(null, 404);
        }

        extract($this->variables);
        $includes = $this->includes;

        ob_start();
        require($path);
        return ob_get_clean();
    }

    /**
     * @throws RouterException
     */
    public function view(): void
    {
        // Check admin permissions
        if ($this->needAdminControl) {
            UsersController::redirectIfNotHavePermissions('core.dashboard');
        }

        extract($this->variables);
        $includes = $this->includes;

        if (is_null($this->customPath) && Utils::containsNullValue($this->package, $this->viewFile)) {
            throw new RouterException("Invalid View usage. Please set a correct path.", 404);
        }

        $path = $this->getViewPath();

        if (!is_file($path)) {
            throw new RouterException(null, 404);
        }

        //Load Elements
        ComponentsManager::getInstance()->loadThemeComponents($this->themeName);

        ob_start();
        require_once $path;
        echo $this->callAlerts();
        $content = ob_get_clean();

        require_once($this->getTemplateFile());
    }

    /**
     * @param array $includes
     * @param string ...$files
     * @return void
     */
    public static function loadInclude(array $includes, #[ExpectedValues(flags: ['beforeScript', 'afterScript', 'beforePhp', 'afterPhp', 'styles'])] string ...$files): void
    {
        foreach ($files as $file) {
            self::loadIncludeFile($includes, $file);
        }
    }

    /**
     * @throws RouterException
     */
    private function callAlerts(): string
    {
        $alerts = Flash::load();
        $alertContent = '';
        foreach ($alerts as $alert) {
            if (!$alert->isAdmin()) {
                $view = new View('Alerts', $alert->getType());
            } else {
                $view = new View('Core', "Alerts/{$alert->getType()}", true);
            }
            $view->addVariable('alert', $alert);
            $alertContent .= $view->loadFile();
        }
        Flash::clear();
        return $alertContent;
    }
}

<?php

namespace CMW\Manager\Views;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Api\APIManager;
use CMW\Manager\Components\ComponentsManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Router\RouterException;
use CMW\Manager\Theme\Editor\EditorRangeOptions;
use CMW\Manager\Theme\Editor\EditorType;
use CMW\Manager\Theme\ThemeManager;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\ExpectedValues;
use function extract;
use function in_array;
use function is_file;
use function is_null;
use function ob_get_clean;
use function ob_start;
use function print_r;

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
    private bool $isPublicView;
    private string $themeName;
    private bool $overrideBackendMode;

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
        $this->isPublicView = false;
        $this->themeName = ThemeManager::getInstance()->getCurrentTheme()->name();
        $this->overrideBackendMode = false;
    }

    /**
     * @param string $package
     * @param string $viewFile
     * @return void
     * @throws RouterException
     */
    public static function basicPublicView(string $package, string $viewFile): void
    {
        $view = new self($package, $viewFile);
        $view->view();
    }

    /**
     * @param string $package
     * @param string $viewFile
     * @return View
     */
    public static function createPublicView(string $package, string $viewFile): View
    {
        $view = new self($package, $viewFile);

        $view->isPublicView = true;

        return $view;
    }

    /**
     * @param string $package
     * @param string $viewFile
     * @return View
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
     * @param bool $overrideBackendMode
     * @return View
     * If true, the view will be displayed even if the backend mode is enabled
     */
    public function setOverrideBackendMode(bool $overrideBackendMode): self
    {
        $this->overrideBackendMode = $overrideBackendMode;
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

        //Backend mode view logic
        $this->backendView();

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
        if ($this->isPublicView) {
            $content = ob_get_clean();
            $editorMode = isset($_GET['editor']) && $_GET['editor'] == '1';
            $content = $this->replaceThemeValues($content, $editorMode);

            // bufferisation de template pour la gestion de replaceThemeValue
            ob_start();
            require_once($this->getTemplateFile());
            $templateContent = ob_get_clean();
            $templateContent = $this->replaceThemeValues($templateContent, $editorMode);
            echo $templateContent;
        } else {
            $content = ob_get_clean();
            require_once($this->getTemplateFile());
        }
    }

    private function replaceThemeValues(string $html, bool $editorMode = false): string
    {
        if ($editorMode) {
            return $html;
        }

        // data-cmw="menu:key"
        $html = preg_replace_callback('/<([a-z0-9]+)([^>]*)data-cmw="([\w-]+):([\w-]+)"([^>]*)>(.*?)<\/\1>/si', function ($m) {
            $tag = $m[1];
            $before = $m[2];
            $menu = $m[3];
            $key = $m[4];
            $after = $m[5];

            $value = ThemeModel::getInstance()->fetchConfigValue($menu, $key);

            return "<{$tag}{$before}{$after}>{$value}</{$tag}>";
        }, $html);


        // data-cmw-style="prop:menu:key[;...]"
        $html = preg_replace_callback('/<([a-z0-9]+)([^>]*)data-cmw-style="([^"]+)"([^>]*)>/i', function ($m) {
            $tag = $m[1];
            $before = $m[2];
            $cmwAttr = $m[3];
            $after = $m[4];

            // 🔍 Extraire le style déjà présent (avant ou après)
            preg_match('/style="([^"]*)"/i', $before . $after, $existingStyleMatch);
            $existingStyles = [];

            if (isset($existingStyleMatch[1])) {
                foreach (explode(';', $existingStyleMatch[1]) as $styleLine) {
                    if (strpos($styleLine, ':') !== false) {
                        [$k, $v] = explode(':', $styleLine, 2);
                        $existingStyles[trim($k)] = trim($v);
                    }
                }
            }

            // 🔁 Générer les styles dynamiques depuis data-cmw-style
            $styles = explode(';', $cmwAttr);
            foreach ($styles as $entry) {
                [$prop, $menu, $key] = explode(':', $entry);
                $val = ThemeModel::getInstance()->fetchConfigValue($menu, $key);
                $editorType = ThemeManager::getInstance()->getEditorType($menu, $key);

                if ($editorType === EditorType::RANGE) {
                    $options = ThemeManager::getInstance()->getEditorRangeOptions($menu, $key);
                    if ($options instanceof EditorRangeOptions) {
                        $val = $options->getPrefix() . $val . $options->getSuffix();
                    }
                }

                $existingStyles[trim($prop)] = $val;
            }

            // 🔄 Nettoyer style et reconstituer
            $cleaned = preg_replace('/style="[^"]*"/i', '', $before . $after);
            $finalStyle = implode('; ', array_map(fn($k, $v) => "$k: $v", array_keys($existingStyles), $existingStyles));

            return "<{$tag} {$cleaned}style=\"{$finalStyle}\">";
        }, $html);


        // data-cmw-class="menu:key [...]"
        $html = preg_replace_callback('/<([a-z0-9]+)([^>]*)data-cmw-class="([^"]+)"([^>]*)>/i', function ($m) {
            $tag = $m[1];
            $before = $m[2];
            $cmwAttr = $m[3];
            $after = $m[4];

            // Récupérer les classes déjà présentes (dans before ou after)
            preg_match('/class="([^"]*)"/i', $before . $after, $existingClassMatch);
            $existingClasses = isset($existingClassMatch[1]) ? explode(' ', $existingClassMatch[1]) : [];

            $refs = explode(' ', $cmwAttr);
            $dynamicClasses = [];

            foreach ($refs as $ref) {
                [$menu, $key] = explode(':', $ref);
                $val = ThemeModel::getInstance()->fetchConfigValue($menu, $key);
                $editorType = ThemeManager::getInstance()->getEditorType($menu, $key);

                if ($editorType === EditorType::RANGE) {
                    $options = ThemeManager::getInstance()->getEditorRangeOptions($menu, $key);
                    if ($options instanceof \CMW\Manager\Theme\Editor\EditorRangeOptions) {
                        $val = $options->getPrefix() . $val . $options->getSuffix();
                    }
                }

                if ($val) {
                    $dynamicClasses[] = $val;
                }
            }

            // Supprimer l'ancien class="..." du before et after
            $cleaned = preg_replace('/class="[^"]*"/i', '', $before . $after);

            // Fusion et reconstruction
            $finalClasses = array_filter(array_merge($existingClasses, $dynamicClasses));
            return "<{$tag} {$cleaned}class=\"" . implode(' ', $finalClasses) . "\">";
        }, $html);


        // data-cmw-visible="menu:key" → suppression de l’élément si valeur = 0
        $html = preg_replace_callback('/<([a-z]+)([^>]+)data-cmw-visible="([\w-]+):([\w-]+)"([^>]*)>(.*?)<\/\1>/si', function ($m) {
            $visible = ThemeModel::getInstance()->fetchConfigValue($m[3], $m[4]);
            if (!$visible || $visible === '0') {
                return ''; // supprimer l’élément entier
            }

            return "<{$m[1]}{$m[2]}{$m[5]}>{$m[6]}</{$m[1]}>";
        }, $html);

        // data-cmw-attr="attr:menu:key [...]"
        $html = preg_replace_callback('/data-cmw-attr="([^"]+)"/', function ($m) {
            $defs = explode(' ', $m[1]);
            $attrs = [];

            foreach ($defs as $def) {
                [$attr, $menu, $key] = explode(':', $def);
                $val = ThemeModel::getInstance()->fetchConfigValue($menu, $key);

                $attrs[] = "{$attr}=\"{$val}\"";
            }

            return implode(' ', $attrs);
        }, $html);

        return $html;
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
                $view = new View('Core', 'Alerts/' . $alert->getType());
            } else {
                $view = new View('Core', 'Alerts/' . $alert->getType(), true);
            }
            $view->addVariable('alert', $alert);
            $alertContent .= $view->loadFile();
        }
        Flash::clear();
        return $alertContent;
    }

    /**
     * @return void
     * @desc Return the view data if the backend mode is enabled
     */
    private function backendView(): void
    {
        $isBackendModeEnabled = EnvManager::getInstance()->getValue('ENABLE_BACKEND_MODE') === 'true';

        if ($this->overrideBackendMode) {
            return;
        }

        if ($isBackendModeEnabled && !$this->needAdminControl) {
            print_r(
                APIManager::createResponse(
                    data: [
                        'package' => $this->package,
                        'viewFile' => $this->viewFile,
                        'viewFilePath' => $this->getViewPath(),
                        'variables' => $this->variables,
                        'includes' => $this->includes,
                    ],
                )
            );
            die();
        }
    }
}

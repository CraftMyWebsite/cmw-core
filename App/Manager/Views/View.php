<?php

namespace CMW\Manager\Views;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Core\MenusController;
use CMW\Controller\Core\ThemeController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Router\RouterException;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Response;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\ExpectedValues;

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

    public function __construct(?string $package = null, ?string $viewFile = null, ?bool $isAdminFile = false, bool $basicVars = true)
    {
        $this->package = $package;
        $this->viewFile = $viewFile;
        $this->includes = $this->generateInclude();
        $this->variables = $basicVars ? $this->generateVariables() : [];
        $this->needAdminControl = false;
        $this->isAdminFile = $isAdminFile;
    }

    /**
     * @throws RouterException
     */
    public static function basicPublicView(string $package, string $viewFile): void
    {
        $view = new self($package, $viewFile);
        $view->view();
    }

    public static function createAdminView(string $package, string $viewFile): View
    {
        $view = new self($package, $viewFile);

        $view->setAdminView()->needAdminControl();

        return $view;
    }

    #[ArrayShape(["styles" => "array", "scripts" => "array"])]
    private function generateInclude(): array
    {
        $array = array("styles" => array(),
            "scripts" => array());

        $array["scripts"]["before"] = array();
        $array["scripts"]["after"] = array();

        return $array;
    }

    #[ArrayShape(["core" => CoreController::class, "menu" => MenusController::class])]
    private function generateVariables(): array
    {
        return array("core" => new CoreController(), "menu" => new MenusController());
    }

    private function addScript(#[ExpectedValues(["after", "before"])] string $position, string $fileName): void
    {
        $this->includes["scripts"][$position][] = $fileName;
    }

    public function setPackage(string $package): self
    {
        $this->package = $package;
        return $this;
    }

    public function setViewFile(string $viewFile): self
    {
        $this->viewFile = $viewFile;
        return $this;
    }

    public function needAdminControl(bool $needAdminControl = true): self
    {
        $this->needAdminControl = $needAdminControl;
        return $this;
    }

    public function setAdminView(bool $isAdminFile = true): self
    {
        $this->isAdminFile = $isAdminFile;
        return $this;

    }

    public function addVariable(string $variableName, mixed $variable): self
    {
        $this->variables[$variableName] ??= $variable;
        return $this;
    }

    public function addVariableList(array $variableList): self
    {
        foreach ($variableList as $key => $value) {
            $this->addVariable($key, $value);
        }

        return $this;
    }

    public function addScriptBefore(string ...$script): self
    {
        foreach ($script as $scriptFile) {
            $this->addScript("before", $scriptFile);
        }

        return $this;
    }

    public function addScriptAfter(string ...$script): self
    {
        foreach ($script as $scriptFile) {
            $this->addScript("after", $scriptFile);
        }

        return $this;
    }

    public function addStyle(string ...$style): self
    {
        foreach ($style as $styleFile) {
            $this->includes["styles"][] = $styleFile;
        }

        return $this;
    }

    public function setCustomPath(string $path): self
    {
        $this->customPath = $path;
        return $this;
    }

    public function setCustomTemplate(string $path): self
    {
        $this->customTemplate = $path;
        return $this;
    }

    private function getViewPath(): string
    {
        if($this->customPath !== null) {
            return $this->customPath;
        }
        $theme = ThemeController::getCurrentTheme()->getName();
        return ($this->isAdminFile)
            ? "App/Package/$this->package/Views/$this->viewFile.admin.view.php"
            : "Public/Themes/$theme/Views/$this->package/$this->viewFile.view.php";
    }

    private function getTemplateFile(): string
    {
        if($this->customTemplate !== null) {
            return $this->customTemplate;
        }
        $theme = ThemeController::getCurrentTheme()->getName();
        return ($this->isAdminFile)
            ? getenv("PATH_ADMIN_VIEW") . "template.php"
            : "Public/Themes/$theme/Views/template.php";
    }

    private static function loadIncludeFile(array $includes, #[ExpectedValues(["beforeScript", "afterScript", "styles"])] string $fileType): void
    {
        if (!in_array($fileType, ["beforeScript", "afterScript", "styles"])) {
            return;
        }

        if ($fileType === "styles") {
            foreach ($includes['styles'] as $style) {
                $styleLink = getenv("PATH_SUBFOLDER") . $style;
                echo <<<HTML
                    <link rel="stylesheet" href="$styleLink">
                HTML;
            }

            return;
        }

        $arrayAccess = $fileType === "beforeScript" ? "before" : "after";

        foreach ($includes['scripts'][$arrayAccess] as $script) {
            $scriptLink = getenv("PATH_SUBFOLDER") . $script;
            echo <<<HTML
                    <script src="$scriptLink"></script>
                HTML;
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

        if ($this->needAdminControl) {
            UsersController::redirectIfNotHavePermissions("core.dashboard");
        }

        if ($this->needAdminControl && isset($_SESSION["cmwUserId"])) {
            $this->addVariableList(array(
                "userAdmin" => (new UsersModel())->getUserById($_SESSION["cmwUserId"])
            ));
        }

        extract($this->variables, EXTR_OVERWRITE);
        $includes = $this->includes;

        if (is_null($this->customPath) && Utils::containsNullValue($this->package, $this->viewFile)) {
            throw new RouterException(null, 404);
        }

        $path = $this->getViewPath();

        if (!is_file($path)) {
            throw new RouterException(null, 404);
        }

        //Show Alerts

        ob_start();
        require_once($path);
        echo $this->callAlerts();
        $content = ob_get_clean();

        require_once($this->getTemplateFile());
    }

    public static function loadInclude(array $includes, #[ExpectedValues(flags: ["beforeScript", "afterScript", "styles"])] string ...$files): void
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
        $alerts = Response::getAlerts();
        $alertContent = "";
        foreach ($alerts as $alert) {
            if(!$alert->isAdmin()) {
                $view = new View("Alerts", $alert->getType());
            } else {
                $view = new View("core", "Alerts/{$alert->getType()}", true);
            }
            $view->addVariable("alert", $alert);
            $alertContent .= $view->loadFile();
        }
        Response::clearAlerts();
        return $alertContent;
    }
}

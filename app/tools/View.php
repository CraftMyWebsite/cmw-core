<?php

namespace CMW\Utils;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Users\UsersController;
use CMW\Model\Users\UsersModel;
use CMW\Router\RouterException;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\ExpectedValues;

class View
{

    private ?string $package;
    private ?string $viewFile;
    private array $includes;
    private array $variables;
    private bool $needAdminControl;
    private bool $isAdminFile;

    public function __construct(?string $package = null, ?string $viewFile = null, ?bool $isAdminFile = false)
    {
        $this->package = $package;
        $this->viewFile = $viewFile;
        $this->includes = $this->generateInclude();
        $this->variables = $this->generateVariables();
        $this->needAdminControl = false;
        $this->isAdminFile = $isAdminFile;
    }

    /**
     * @throws \CMW\Router\RouterException
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

    #[ArrayShape(["core" => CoreController::class])]
    private function generateVariables(): array
    {
        return array("core" => new CoreController());
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
            /**
             * @var string $key
             * @var mixed $value
             */
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

    private function getViewPath(): string
    {
        $theme = (new CoreController())->cmwThemePath();
        return ($this->isAdminFile)
            ? "app/package/{$this->package}/views/{$this->viewFile}.admin.view.php"
            : "public/themes/$theme/views/{$this->package}/{$this->viewFile}.view.php";
    }

    private function getTemplateFile(): string
    {
        $theme = (new CoreController())->cmwThemePath();
        return ($this->isAdminFile)
            ? getenv("PATH_ADMIN_VIEW") . "template.php"
            : "public/themes/$theme/views/template.php";
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
     * @throws \CMW\Router\RouterException
     */
    public function loadFile(): string
    {
        $path = $this->getViewPath();

        if (!is_file($path)) {
            throw new RouterException(null, 404);
        }

        extract($this->variables, EXTR_OVERWRITE);
        $includes = $this->includes;

        ob_start();
        require($path);
        return ob_get_clean();
    }

    /**
     * @throws \CMW\Router\RouterException
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

        if (Utils::hasOneNullValue($this->package, $this->viewFile)) {
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
     * @throws \CMW\Router\RouterException
     */
    private function callAlerts(): string
    {
        $alerts = Response::getAlerts();
        $alertContent = "";
        foreach ($alerts as $alert) {
            $view = new View("alerts", $alert->getType());
            $view->addVariable("alert", $alert);
            $alertContent .= $view->loadFile();
        }
        Response::clearAlerts();
        return $alertContent;
    }
}
<?php

namespace CMW\Manager\Views;

use CMW\Manager\Loader\Loader;
use CMW\Manager\Views\Implementations\BaseViewImplementation;
use JetBrains\PhpStorm\ExpectedValues;
use function array_reduce;

class View
{
    private static ?IView $_instance = null;

    /**
     * @param string|null $package
     * @param string|null $viewFile
     * @param bool|null $isAdminFile
     */
    public function __construct(?string $package = null, ?string $viewFile = null, ?bool $isAdminFile = false)
    {
        $instance = self::getInstance();

        $instance->setPackage($package);
        $instance->setViewFile($viewFile);
        $instance->setAdminView($isAdminFile);
    }

    public static function getInstance(): IView
    {
        if (self::$_instance === null) {
            self::$_instance = self::loadViewInstance();
        }

        return self::$_instance;
    }

    private static function loadViewInstance(): IView
    {
        return self::getHighestImplementation() ?? BaseViewImplementation::getInstance();
    }

    private static function getHighestImplementation(): ?IView
    {
        $implementations = Loader::loadManagerImplementations(IView::class, 'Views');

        return array_reduce($implementations, static function (?IView $highest, IView $current) {
            return ($highest === null || $current->weight() > $highest->weight()) ? $current : $highest;
        });
    }


    /**
     * @param string $package
     * @param string $viewFile
     * @return void
     */
    public static function basicPublicView(string $package, string $viewFile): void
    {
        self::getInstance()->basicPublicView($package, $viewFile);
    }

    /**
     * @param string $package
     * @param string $viewFile
     * @return IView
     */
    public static function createPublicView(string $package, string $viewFile): IView
    {
        return self::getInstance()->createPublicView($package, $viewFile);
    }

    /**
     * @param string $package
     * @param string $viewFile
     * @return IView
     */
    public static function createAdminView(string $package, string $viewFile): IView
    {
        return self::getInstance()->createAdminView($package, $viewFile);
    }

    /**
     * @param string $package
     * @return IView
     */
    public function setPackage(string $package): IView
    {
        return self::getInstance()->setPackage($package);
    }

    /**
     * @param string $viewFile
     * @return IView
     */
    public function setViewFile(string $viewFile): IView
    {
        return self::getInstance()->setViewFile($viewFile);
    }

    /**
     * @param bool $needAdminControl
     * @return IView
     */
    public function needAdminControl(bool $needAdminControl = true): IView
    {
        return self::getInstance()->needAdminControl($needAdminControl);
    }

    /**
     * @param bool $isAdminFile
     * @return IView
     */
    public function setAdminView(bool $isAdminFile = true): IView
    {
        return self::getInstance()->setAdminView($isAdminFile);
    }

    /**
     * @param string $variableName
     * @param mixed $variable
     * @return IView
     */
    public function addVariable(string $variableName, mixed $variable): IView
    {
        return self::getInstance()->addVariable($variableName, $variable);
    }

    /**
     * @param array $variableList
     * @return IView
     */
    public function addVariableList(array $variableList): IView
    {
        return self::getInstance()->addVariableList($variableList);
    }

    /**
     * @param string ...$script
     * @return IView
     */
    public function addScriptBefore(string ...$script): IView
    {
        return self::getInstance()->addScriptBefore(...$script);
    }

    /**
     * @param string ...$script
     * @return IView
     */
    public function addScriptAfter(string ...$script): IView
    {
        return self::getInstance()->addScriptAfter(...$script);
    }

    /**
     * @param string ...$php
     * @return IView
     */
    public function addPhpBefore(string ...$php): IView
    {
        return self::getInstance()->addPhpBefore(...$php);
    }

    /**
     * @param string ...$php
     * @return IView
     */
    public function addPhpAfter(string ...$php): IView
    {
        return self::getInstance()->addPhpAfter(...$php);
    }

    /**
     * @param string ...$style
     * @return IView
     */
    public function addStyle(string ...$style): IView
    {
        return self::getInstance()->addStyle(...$style);
    }

    /**
     * @param string $path
     * @return IView
     */
    public function setCustomPath(string $path): IView
    {
        return self::getInstance()->setCustomPath($path);
    }

    /**
     * @param string $path
     * @return IView
     */
    public function setCustomTemplate(string $path): IView
    {
        return self::getInstance()->setCustomTemplate($path);
    }

    /**
     * @param bool $overrideBackendMode
     * @return IView
     * If true, the view will be displayed even if the backend mode is enabled
     */
    public function setOverrideBackendMode(bool $overrideBackendMode): IView
    {
        return self::getInstance()->setOverrideBackendMode($overrideBackendMode);
    }

    public function view(): void
    {
        self::getInstance()->view();
    }

    /**
     * @param array $includes
     * @param string ...$files
     * @return void
     */
    public static function loadInclude(array $includes, #[ExpectedValues(flags: ['beforeScript', 'afterScript', 'beforePhp', 'afterPhp', 'styles'])] string ...$files): void
    {
        self::getInstance()->loadInclude($includes, ...$files);
    }
}

<?php

namespace CMW\Manager\Views;

use JetBrains\PhpStorm\ExpectedValues;

interface IView
{
    public function weight(): int;

    public static function getInstance(): self;

    public function setPackage(string $package): self;

    public function setViewFile(string $viewFile): self;

    public function needAdminControl(bool $needAdminControl = true): self;

    public function setAdminView(bool $isAdminFile = true): self;

    public function addVariable(string $variableName, mixed $variable): self;

    public function addVariableList(array $variableList): self;

    public function addScriptBefore(string ...$script): self;

    public function addScriptAfter(string ...$script): self;

    public function addPhpBefore(string ...$php): self;

    public function addPhpAfter(string ...$php): self;

    public function addStyle(string ...$style): self;

    public function setCustomPath(string $path): self;

    public function setCustomTemplate(string $path): self;

    public function setOverrideBackendMode(bool $overrideBackendMode): self;

    public static function loadInclude(array $includes, #[ExpectedValues(flags: ['beforeScript', 'afterScript', 'beforePhp', 'afterPhp', 'styles'])] string ...$files): void;

    public function basicPublicView(string $package, string $viewFile): void;

    public function createPublicView(string $package, string $viewFile): self;

    public static function createAdminView(string $package, string $viewFile): self;

    public function view(): void;
}
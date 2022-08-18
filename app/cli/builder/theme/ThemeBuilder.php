<?php

require_once("app/cli/CliBuilder.php");

class ThemeBuilder extends CliBuilder{

    protected string $themeName;
    protected string $themeVersion;
    protected string $themeAuthor;
    protected int $themeCmwVersion;
    protected ?array $themeDependPackages;

    public function __construct()
    {
        parent::__construct();

        $this->init(); //Builder setup (wizard)

        $this->build(); //Create the theme with all the datas
    }

    private function init()
    {
        self::setThemeName();
        self::setThemeVersion();
        self::setThemeAuthor();
    }


    private function setThemeName(): void
    {
        $this->say(CLI_THEME_BUILDER_NAME);
        $this->themeName = $this->read();
    }

    private function setThemeVersion(): void
    {
        $this->say(CLI_THEME_BUILDER_VERSION);
        $this->themeVersion = $this->read();
    }

    private function setThemeAuthor(): void
    {
        $this->say(CLI_THEME_BUILDER_AUTHOR);
        $this->themeAuthor = $this->read();
    }


    private function build(): void
    {
        $this->say("Theme $this->themeName, $this->themeVersion, $this->themeAuthor");
    }

}

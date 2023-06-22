<?php
namespace CMW\Cli\Builder\Theme;

use CMW\Cli\CliBuilder;

require_once("App/Cli/CliBuilder.php");

class ThemeBuilder extends CliBuilder{

    protected string $themeName;
    protected string $themeVersion;
    protected string $themeAuthor;
    protected string $themeCmwVersion;
    protected ?array $themeDependPackages;
    protected ?array $themeExtensions;

    private array $availableExtensions = ["Bootstrap", "Tailwind", "jQuery", "FontAwesome"];

    /**
     * @throws \JsonException
     */
    public function __construct()
    {
        parent::__construct();

        $this->init(); //Builder setup (wizard)

        $this->build(); //Create the Theme with all the datas

        $this->sayLn(CLI_THEME_BUILD_SUCCESS);
    }

    private function init(): void
    {
        $this->setThemeName();
        $this->setThemeVersion();
        $this->setThemeAuthor();
        $this->setThemeCmwVersion();
        $this->setThemeExtensions();
    }


    private function setThemeName(): void
    {
        $this->sayLn(CLI_THEME_BUILDER_NAME);

        $this->themeName = trim($this->read());
        //TODO Check if the Theme name is not already use
    }

    private function setThemeVersion(): void
    {
        $this->sayLn(CLI_THEME_BUILDER_VERSION);
        $this->themeVersion = $this->read();
    }

    private function setThemeAuthor(): void
    {
        $this->sayLn(CLI_THEME_BUILDER_AUTHOR);
        $this->themeAuthor = $this->read();
    }

    private function setThemeCmwVersion(): void
    {
        $this->sayLn(CLI_THEME_BUILDER_CMW_VERSION . "XX");
        $this->themeCmwVersion = $this->read();
    }

    private function setThemeExtensions(): void
    {
        $this->sayLn(CLI_THEME_BUILDER_EXTENSIONS);

        $i = 0;
        foreach ($this->availableExtensions as $extension){
            $this->sayLn($i . ") " . $extension);

            ++$i;
        }
        $this->themeExtensions = explode(' ', $this->read());
    }


    /**
     * @throws \JsonException
     */
    private function build(): void
    {
        $this->say($this->themeName, $this->themeVersion, $this->themeAuthor, $this->themeCmwVersion);
        foreach ($this->themeExtensions as $extension){
            $this->say($extension);
        }

        //Launch the build process
        require_once("App/Cli/Builder/Theme/ThemeBuilderInstallation.php");
        $themeBuilderInstallation = new ThemeBuilderInstallation();
        $themeBuilderInstallation->generateTheme($this->themeName, $this->themeVersion, $this->themeAuthor,
            $this->themeCmwVersion, $this->themeExtensions);
    }

}

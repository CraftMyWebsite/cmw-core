<?php


class ThemeBuilderInstallation
{

    public string $themePath = "public/themes/";


    /**
     * @throws \JsonException
     */
    public function generateTheme(string $name, string $version, string $author, string $cmwVersion, array $extensions): void
    {
        $path = $this->themePath . $name;

        $this->createDirectory($path); //Create the theme folder
        $this->createJsonFile("infos", $path, $name, $version, $author, $cmwVersion); //Create the info.json file
        $this->createDirectories($path);
        $this->createFiles($path);
    }

    private function generateExtensions(): void
    {
        //todo generate folders, files and basic functions
    }

    private function downloadExtensions(): void
    {

    }


    /* Download specific extensions */
    private function downloadBootstrap(): void
    {

    }

    private function downloadTailWind(): void
    {

    }

    private function downloadJQuery(): void
    {

    }

    private function downloadFontAwesome(): void
    {

    }

    private function createDirectories(string $path): void
    {
        //Create assets and views folders
        $this->createDirectory($path . "/assets");
        $this->createDirectory($path . "/views");

        //Create defaults directories
        $defaultViewsDir = array("alerts", "core", "errors", "includes", "pages", "users");

        foreach ($defaultViewsDir as $item) {
            $this->createDirectory($path . "/views/" . $item);
        }
    }

    private function createFiles(string $path): void
    {
        $defaultViewFiles = array("templates.php", "includes/footer.inc.php", "includes/head.inc.php", "includes/header.inc.php");

        foreach ($defaultViewFiles as $file) {
            $this->createFile($path . "/views/" . $file);
        }

    }


    /**
     * @param string $dirName
     * @return void
     * @Desc Create directory on the theme folder
     */
    private function createDirectory(string $dirName): void
    {
        if (!file_exists($dirName)
            && !mkdir($concurrentDirectory = $dirName)
            && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
    }

    /**
     * @throws \JsonException
     */
    private function createJsonFile(string $fileName, string $path, string $name, string $version, string $author, string $cmwVersion): void
    {
        $content = json_encode(array("creator" => $author, "name" => $name, "version" => $version,
                                        "cmwVersion" => $cmwVersion), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);

        file_put_contents("$path/$fileName.json", $content);
    }

    private function createFile(string $fileName, string $content = ""): void
    {
        file_put_contents($fileName, $content);
    }

}
<?php

namespace CMW\Cli\Builder\Package;

use CMW\Cli\CliBuilder;

require_once("App/Cli/CliBuilder.php");

class PackageBuilder extends CliBuilder
{
    private string $packageName;
    private string $authorName;
    private int $menuType;

    public function __construct()
    {
        parent::__construct();

        $this->init();
        $this->generate();
    }

    private function init(): void
    {
        $this->setPackageName();
        $this->setAuthorName();
        $this->setMenuType();
    }

    private function generate(): void
    {
        $this->sayLn("Lancement de la génération de votre package " . $this->packageName);

        require_once("App/Cli/Builder/Package/PackageBuilderGeneration.php");
        $builder = new PackageBuilderGeneration();
        $builder->generatePackage($this->packageName, $this->authorName, $this->menuType);

        $this->sayLn("Génération de votre package " . $this->packageName . " terminée.
                                \n Retrouvez les fichiers ici: App/Package/" . $this->packageName);
    }

    private function setPackageName(): void
    {
        $this->sayLn("Entrez le nom de votre package:");
        $this->packageName = ucfirst(trim($this->read()));
    }

    private function setAuthorName(): void
    {
        $this->sayLn("Entrez votre pseudo:");
        $this->authorName = trim($this->read());
    }

    private function setMenuType():void
    {

            $this->sayLn("Quel type de menu voulez-vous: 
        \n 1) Menu classique
        \n 2) Menu dépliant
        \n Tapez le numéro que vous souhaitez: 1 OU 2");
            $this->menuType = trim($this->read());
    }


}
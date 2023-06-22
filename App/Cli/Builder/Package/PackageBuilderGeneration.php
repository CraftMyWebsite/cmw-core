<?php

namespace CMW\Cli\Builder\Package;

use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use RuntimeException;

class PackageBuilderGeneration
{
    private string $path;

    public function generatePackage(string $packageName, string $authorName, int $menuType): void
    {
        $path = "App/Package/$packageName/";
        $this->path = $path;

        $this->createDirectory($path);
        $this->createInfoFile($packageName, $authorName, $menuType);
        $this->createDirectories($path);

        $this->createController($packageName, $authorName);
        $this->createEntities($packageName);
        $this->createModel($packageName, $authorName);
        $this->createImplementations($packageName);
        $this->createInit();
        $this->createLang();
        $this->createView($packageName);
    }

    private function createDirectory(string $folderName): void
    {
        if (!file_exists($folderName)
            && !mkdir($concurrentDirectory = $folderName)
            && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
    }

    private function createInfoFile(string $packageName, string $authorName, int $menuType): void
    {
        if ($menuType === 1) {
            $menu = [
                "name_menu_fr" => $packageName,
                "name_menu_en" => $packageName,
                "icon_menu" => "fas fa-helmet-safety",
                "url_menu" => "$packageName/demo"];
        } else {
            $menu = [
                "name_menu_fr" => $packageName,
                "name_menu_en" => $packageName,
                "icon_menu" => "fas fa-th",
                "url_menu" => "",
                "urls_submenu_fr" => [
                    "Demo" => "$packageName/demo",
                ],
                "urls_submenu_en" => [
                    "Demo" => "demo",
                ],
            ];
        }


        $content = json_encode([
            "name" => $packageName,
            "menus" => [$menu],
            "description" => [
                [
                    "fr" => "Package généré avec CMW CLI",
                    "en" => "Package generated with CMW CLI"
                ]
            ],
            "version" => "1.0.0",
            "author" => $authorName,
            'isGame' => false,
            'isCore' => false
        ],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);

        file_put_contents("App/Package/$packageName/infos.json", $content);
    }

    private function createDirectories(string $path): void
    {
        //Create defaults directories
        $defaultDirs = ["Controllers", "Entities", "Implementations", "Init", "Lang", "Models", "Views"];

        foreach ($defaultDirs as $dir) {
            $this->createDirectory($path . $dir);
        }
    }

    private function createFile(string $name, mixed $data): void
    {
        $fp = fopen($this->path . $name, 'wb');
        fwrite($fp, $data);
        fclose($fp);
    }

    private function createController(string $packageName, string $authorName): void
    {
        $data = <<<PHP
<?php

namespace CMW\Controller\\$packageName;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;

/**
 * Class: @{$packageName}Controller
 * @package {$packageName}
 * @author {$authorName}
 * @version 1.0
 */
class {$packageName}Controller extends AbstractController
{
    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin/{$packageName}")]
    #[Link("/demo", Link::GET, [], "/cmw-admin/{$packageName}")]
    public function demo(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "demo.show");

        //Include the view file ("Views/demo.admin.view.php").
        View::createAdminView('{$packageName}', 'demo')
            ->view();
    }
}
PHP;

        $this->createFile('Controllers/' . $packageName . 'Controller.php', $data);
    }

    private function createEntities(string $packageName): void
    {
        $data = <<<PHP
<?php

namespace CMW\Entity\\$packageName;


class {$packageName}Entity
{

  
}
PHP;
        $this->createFile('Entities/' . $packageName . 'Entity.php', $data);
    }

    private function createModel(string $packageName, string $authorName): void
    {
        $data = <<<PHP
<?php

namespace CMW\Model\\$packageName;

use CMW\Manager\Package\AbstractModel;
use CMW\Manager\Database\DatabaseManager;

/**
 * Class @{$packageName}Model
 * @package {$packageName}
 * @author {$authorName}
 * @version 1.0
 */
class {$packageName}Model extends AbstractModel
{
    public function doSomething(): ?string
    {
        return DatabaseManager::getInstance()->query('SELECT * FROM demo')->fetchAll();
    }
}

PHP;

        $this->createFile('Models/' . $packageName . 'Model.php', $data);
    }

    private function createImplementations(string $packageName): void
    {
        $data = <<<PHP
<?php

namespace CMW\Implementation\\$packageName;

use CMW\Interface\Core\IMenus;
use CMW\Manager\Lang\LangManager;

class {$packageName}MenusImplementations implements IMenus {

    public function getRoutes(): array
    {
        return [
            "$packageName" => "$packageName"
        ];
    }

    public function getPackageName(): string
    {
        return "$packageName";
    }
}
PHP;
        $this->createFile('Implementations/' . $packageName . 'MenusImplementations.php', $data);
    }

    private function createInit(): void
    {
        $data = "CREATE TABLE IF NOT EXISTS `builder`(
                `builder_id` INT NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`builder_id`)
                ) ENGINE = InnoDB
                DEFAULT CHARSET = utf8mb4;";
        $this->createFile("Init/init.sql", $data);

        $this->createFile("Init/permissions.json",
            json_encode(['demo.show'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR |
                JSON_PRETTY_PRINT));
    }

    private function createLang(): void
    {
        $dataEn = <<<PHP
<?php

    return [
        "demo" => "Demo Builder EN",
        "title" => "This is a title"
    ];
?>
PHP;

        $dataFr = <<<PHP
<?php

    return [
        "demo" => "Demo Buildeur FR",
        "title" => "Ceci est un titre"
    ];
?>
PHP;

        $this->createFile("Lang/en.php", $dataEn);
        $this->createFile("Lang/fr.php", $dataFr);
    }

    private function createView(string $packageName): void
    {
        $title = '$title';
        $description = '$description';

        $data = <<<PHP
<?php

use CMW\Manager\Lang\LangManager;

$title = LangManager::translate("$packageName.title");
$description = LangManager::translate("$packageName.demo");
?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-circle-question"></i> <span
            class="m-lg-auto">Demo $packageName</span></h3>
</div>
<section class="row">
    <div class="col-12 col-lg-3">
        <div class="card">
            (☞ﾟヮﾟ)☞
        </div>
    </div>
</section>
PHP;

        $this->createFile("Views/demo.admin.view.php", $data);

    }
}
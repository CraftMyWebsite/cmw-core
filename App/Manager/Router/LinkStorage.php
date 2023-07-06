<?php

namespace CMW\Manager\Router;


use CMW\Manager\Class\ClassManager;
use CMW\Manager\Class\PackageManager;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Utils\Utils;
use ReflectionClass;
use ReflectionException;

/**
 * Class: @LinkStorage
 * @package Core
 * @author CraftMywebsite <contact@craftmywebsite.fr>
 * @version 1.0
 */
class LinkStorage
{
    private array $fileLoaded = [];

    private static LinkStorage $_instance;

    public static function getInstance(): self
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }


    /**
     * @return void
     * @desc Store all Default routes to the DB
     */
    public function storeDefaultRoutes(): void
    {
        foreach ($this->getAllRoutes() as $package => $routes) {
            /* @var Link $route */
            foreach ($routes as $route) {
                if ($route->getPath() !== "/") {
                    $slug = $route->getScope() . $route->getPath();
                    $title = $route->getName() === '1' ?: Utils::normalizeForSlug($route->getPath());
                    $method = $route->getMethod();
                    $isAdmin = str_contains(mb_strtolower($route->getScope() ?? "/"), "cmw-admin");
                    $isDynamic = $route->getVariables() !== [];
                    $weight = $route->getWeight();

                    $this->storeRoute($slug, $package, $title, $method, $isAdmin, $isDynamic, $weight);
                }
            }
        }
    }


    /**
     * @return \CMW\Manager\Router\Link[]
     * @desc Get all packages routes, and return Link[] entity.
     */
    public function getAllRoutes(): array
    {
        $toReturn = [];

        $packageFolder = 'App/Package';
        $contentDirectory = array_diff(scandir("$packageFolder/"), array('..', '.'));
        $dir = EnvManager::getInstance()->getValue("dir");
        foreach ($contentDirectory as $package) {
            $packageSubFolder = "$packageFolder/$package/Controllers";
            if (is_dir($packageSubFolder)) {
                $contentSubDirectory = array_diff(scandir("$packageSubFolder/"), array('..', '.'));
                foreach ($contentSubDirectory as $packageFile) {
                    $file = "$dir$packageSubFolder/$packageFile";
                    if (is_file($file)) {
                        $toReturn[$package] = $this->getControllerRoutes($file, $package);
                    }
                }
            }
        }

        return $toReturn;
    }


    /**
     * @param string $file
     * @return array
     */
    public function getControllerRoutes(string $file): array
    {
        $toReturn = [];

        if (in_array($file, $this->fileLoaded, true)) {
            return [];
        }

        $className = PackageManager::getClassNamespaceFromPath($file);

        try {
            $classRef = new ReflectionClass($className);
            foreach ($classRef->getMethods() as $method) {

                $isMethodClass = $method->getDeclaringClass()->getName() === $className;

                if (!$isMethodClass) {
                    continue;
                }

                $linkAttributes = $method->getAttributes(Link::class);
                foreach ($linkAttributes as $attribute) {

                    /** @var Link $linkInstance */
                    $toReturn[] = $attribute->newInstance();
                }

            }
            $this->fileLoaded[] = $file;

            return $toReturn;
        } catch (ReflectionException) {
        }

        return [];
    }

    /**
     * @param string $slug
     * @param string $package
     * @param string $title
     * @param string $method
     * @param bool $isAdmin
     * @param bool $isDynamic
     * @param int $weight
     * @return void
     * @desc Store routes to DB
     */
    public function storeRoute(string $slug, string $package, string $title, string $method, bool $isAdmin, bool $isDynamic, int $weight): void
    {
        /* Add '/' if the slug don't start with this symbol... */
        if ($slug[0] !== "/") {
            $slug = "/" . $slug;
        }

        $var = array(
            "slug" => $slug,
            "package" => $package,
            "title" => $title,
            "method" => mb_strtoupper($method),
            "isAdmin" => !empty($isAdmin) ?: 0,
            "isDynamic" => !empty($isDynamic) ?: 0,
            "weight" => $weight
        );

        $sql = "INSERT INTO cmw_core_routes (core_routes_slug, core_routes_package, core_routes_title, 
                             core_routes_method, core_routes_is_admin, core_routes_is_dynamic, core_routes_weight)
                VALUES (:slug, :package, :title, :method, :isAdmin, :isDynamic, :weight)";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute($var);
    }

    /**
     * @param int $id
     * @return void
     */
    public function deleteRouteById(int $id): void
    {
        $sql = "DELETE FROM cmw_core_routes WHERE core_routes_id = :id";
        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);
        $req->execute(["id" => $id]);
    }

    /**
     * @return void
     * @Desc clear the cmw_core_routes table and reset the AI value
     */
    public function deleteAllRoutes(): void
    {
        $sql = "TRUNCATE TABLE `cmw_core_routes`";
        $db = DatabaseManager::getInstance();
        $db->query($sql);
    }

    /**
     * @param int $id
     * @param string $slug
     * @param string $package
     * @param string $title
     * @param string $method
     * @param bool $isAdmin
     * @param bool $isDynamic
     * @param int $weight
     * @return void
     */
    public function updateRouteById(int $id, string $slug, string $package, string $title, string $method, bool $isAdmin, bool $isDynamic, int $weight): void
    {
        /* Add '/' if the slug don't start with this symbol... */
        if ($slug[0] !== "/") {
            $slug = "/" . $slug;
        }

        $var = array(
            "id" => $id,
            "slug" => $slug,
            "package" => $package,
            "title" => $title,
            "method" => mb_strtoupper($method),
            "isAdmin" => !empty($isAdmin) ?: 0,
            "isDynamic" => !empty($isDynamic) ?: 0,
            "weight" => $weight
        );

        $sql = "UPDATE cmw_core_routes SET core_routes_slug = :slug, core_routes_package = :package, 
                           core_routes_title = :title, core_routes_method = :method, core_routes_is_admin = :isAdmin,
                           core_routes_is_dynamic = :isAdmin, core_routes_weight = :weight WHERE core_routes_id = :id";
        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);
        $req->execute($var);
    }
}
<?php

namespace CMW\Router;

use JetBrains\PhpStorm\ArrayShape;

/**
 * Class: @route
 * @package Core
 * @author CraftMywebsite <contact@craftmywebsite.fr>
 * @version 1.0
 */
class Route
{

    private string $path;
    private string $name;
    private int $weight;
    /** @var callable $callable */
    private $callable;
    private array $matches = [];
    private array $params = [];

    public function __construct($path, $callable, $weight = 1, string $name = "")
    {
        $this->path = trim($path, '/');
        $this->weight = $weight;
        $this->callable = $callable;
        $this->name = $name;
    }

    public function __debugInfo(): ?array
    {
        $toReturn = array();

        $toReturn["path"] = $this->path;
        $toReturn["name"] = $this->name;
        $toReturn["weight"] = $this->weight;
        if (!empty($this->matches)) {
            $toReturn["matches"] = $this->matches;
        }
        if (!empty($this->params)) {
            $toReturn["params"] = $this->params;
        }

        return $toReturn;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function with($param, $regex): Route
    {
        $this->getParams()[$param] = str_replace('(', '(?:', $regex);
        return $this;
    }

    /**
     * Permettra de capturer l'url avec les paramètre
     * get('/posts/:slug-:id') par exemple
     * @param $url
     * @return bool
     */
    public function match($url): bool
    {
        $url = trim($url, '/');
        $path = preg_replace_callback('#:(\w+)#', [$this, 'paramMatch'], $this->path);
        $regex = "#^$path$#i";

        if (!preg_match($regex, $url, $matches)) {
            return false;
        }

        array_shift($matches);
        $this->matches = $matches;
        return true;
    }

    private function paramMatch($match): string
    {
        if (isset($this->params[$match[1]])) {
            return '(' . $this->params[$match[1]] . ')';
        }
        return '([^/]+)';
    }

    public function call()
    {
        return call_user_func_array($this->callable, $this->matches);
    }

    public function getUrl(array $params = array()): string
    {
        $path = $this->path;
        foreach ($params as $k => $v) {
            $path = str_replace(":$k", $v, $path);
        }
        return $path;
    }
}
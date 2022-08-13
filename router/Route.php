<?php

namespace CMW\Router;

/**
 * Class: @route
 * @package Core
 * @author CraftMywebsite <contact@craftmywebsite.fr>
 * @version 1.0
 */
class Route
{

    private string $path;
    private int $weight;
    private $callable;
    private array $matches = [];
    private array $params = [];

    public function __construct($path, $callable, $weight = 1)
    {
        $this->path = trim($path, '/');
        $this->weight = $weight;
        $this->callable = $callable;
    }

    public function getWeight(): int {
        return $this->weight;
    }

    public function with($param, $regex): Route
    {
        $this->params[$param] = str_replace('(', '(?:', $regex);
        return $this; // On retourne tjrs l'objet pour enchainer les arguments
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
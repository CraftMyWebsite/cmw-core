<?php

namespace CMW\Router;

/**
 * Class: @route
 * @package Core
 * @author CraftMywebsite <contact@craftmywebsite>
 * @version 1.0
 */
class route
{

    private string $path;
    private $callable;
    private array $matches = [];
    private array $params = [];

    public function __construct($path, $callable)
    {
        $this->path = trim($path, '/');  // On retire les / inutiles
        $this->callable = $callable;
    }

    public function with($param, $regex): route
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
        $path = preg_replace_callback('#:([\w]+)#', [$this, 'paramMatch'], $this->path);
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
        if (is_string($this->callable)) {
            $params = explode('#', $this->callable);
            if ($params[0] === "core") {
                $controller = "CMW\\Controller\\" . $params[0] . "Controller";
            } else {
                $controller = "CMW\\Controller\\" . $params[0] . "\\" . $params[0] . "Controller";
            }

            $controller = new $controller();
            return call_user_func_array([$controller, $params[1]], $this->matches);
        }

        return call_user_func_array($this->callable, $this->matches);
    }

    public function getUrl($params)
    {
        $path = $this->path;
        foreach ($params as $k => $v) {
            $path = str_replace(":$k", $v, $path);
        }
        return $path;
    }
}
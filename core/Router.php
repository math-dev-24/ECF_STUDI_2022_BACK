<?php

class Router
{
    private array $routes;


    public function post(string $path, callable|array $action):void
    {
        $this->routes['POST'][$path] = $action;
    }

    public function get(string $path, callable|array $action):void
    {
        $this->routes['GET'][$path] = $action;
    }

    public function put(string $path, callable|array $action):void
    {
        $this->routes['PUT'][$path] = $action;
    }

    public function resolve(string $uri, string $method):mixed
    {
        $path = explode('?',$uri)[0];
        $action = $this->routes[$method][$path] ?? null;

        if (is_callable($action))
        {
            return $action();
        }
        if (is_array($action))
        {
            [$className, $method] = $action;
            if (class_exists($className) && method_exists($className,$method))
            {
                $class = new $className();
                call_user_func_array([$class, $method], []);
            }
        }

        throw new Exception($uri);
    }
}
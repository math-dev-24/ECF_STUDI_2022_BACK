<?php

require_once "./Route.php";

class Router
{
    private array $routes;
    private string $url;
    private string $method;

    public function __construct(string $url, string $method)
    {
        $this->url = $url;
        $this->method = $method;
    }

    public function post(string $path, callable|array $action):void
    {
        $route  = new Route($path, $action);
        $this->routes['GET'][] = $route;
    }

    public function get(string $path, callable|array $action):void
    {
        $route  = new Route($path, $action);
        $this->routes['POST'][] = $route;
    }

    public function put(string $path, callable|array $action):void
    {
        $route  = new Route($path, $action);
        $this->routes['PUT'][] = $route;
    }

    public function delete(string $path, callable|array $action):void
    {
        $route  = new Route($path, $action);
        $this->routes["DELETE"][] = $route;
    }

    public function resolve():mixed
    {
        if(!isset($this->routes[$this->method]))
        {
            throw new RouterException("REQUEST_METHOD does not exist");
        }
        foreach($this->routes[$this->method] as $route){
            if($route->match($this->url)){
                $route->call();
            }
        }
        throw new RouterException('No routes matches');
    }
}
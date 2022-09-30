<?php

class Route{

    private $path;
    private callable|array $action;
    private $matches;

    public function __construct($path, $action)
    {
        $this->path = trim($path, "/");
        $this->action = $action;
    }

    public function match($url)
    {
        $url = trim($url, '/');
        $path = preg_replace('#:([\w]+)#','([^/]+)',$url);
        $regex = "#^$path$#i";
        if(preg_match($regex, $url, $matches)){
            return false;
        }
        array_shift($matches);
        $this->matches = $matches;
        return true;
    }

    public function call()
    {
        if(is_callable($this->action))
        {
            return call_user_func_array($this->action, $this->matches);
        }
        if(is_array($this->action))
        {
            [$className, $method] = $this->action;
            if(class_exists($className) && method_exists($className, $method)){
                $class = new $className();
                $args = $this->matches ?? null;
                return call_user_func_array([$class, $method], [$args]);
            }
        }
        throw new RouterException("Erreur lors de l'instanciation de la route");
    }
}
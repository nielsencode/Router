<?php
namespace Components\Router\Router;

require_once __DIR__.'/Route.php';

class Router
{
    protected $routes = [
        'get' => [],
        'post' => []
    ];

    protected function registerRoute($method,$uri,$callback)
    {
        $this->routes[$method][$uri] = new Route($uri,$callback);
    }

    public function get($uri,$callback)
    {
        $this->registerRoute(__FUNCTION__,$uri,$callback);
    }

    public function post($uri,$callback)
    {
        $this->registerRoute(__FUNCTION__,$uri,$callback);
    }

    protected function findRoute($method,$uri)
    {
        foreach ($this->routes[$method] as $route) {
            if ($route->matchesUri($uri)) {
                return $route;
            }
        }

        return false;
    }

    public function run()
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        $uri = $_SERVER['REQUEST_URI'];

        if (!$route = $this->findRoute($method,$uri)) {
            throw new \Exception("No route defined for \"$uri\".");
        }

        $parameters = $route->getParameters($uri);

        return $route->load($parameters);
    }
}

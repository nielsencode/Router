<?php

namespace Router;

require_once __DIR__.'/RouterException.php';
require_once __DIR__.'/Route.php';

class Router
{
    /**
     * The configs.
     *
     * @var array
     */
    protected $configs = [];

    /**
     * The registered routes.
     *
     * @var array
     */
    protected $routes = [];

    /**
     * Create a new Router instance.
     *
     * @param array $configs ['domain'=>string]
     */
    public function __construct($configs)
    {
        $this->configs = $configs;
    }

    /**
     * Get a configuration value.
     *
     * @param string $config
     * @return mixed
     * @throws RouterException
     */
    public function getConfig($name)
    {
        if (!isset($this->configs[$name])) {
            throw new RouterException("Config \"$name\" is undefined.");
        }

        return $this->configs[$name];
    }

    /**
     * Register a route that responds to the GET method.
     *
     * @param string $route
     * @param callable|array $callback
     */
    public function get($route,$callback)
    {
        $this->register('get',$route,$callback);
    }

    /**
     * Register a route that responds to the POST method.
     *
     * @param string $route
     * @param callable|array $callback
     */
    public function post($route,$callback)
    {
        $this->register('post',$route,$callback);
    }

    /**
     * Register a route.
     *
     * @param string $method
     * @param string $route
     * @param callable|array $callback
     */
    public function register($method,$route,$callback)
    {
        $this->routes[] = new Route($method,$route,$callback);
    }

    /**
     * Get the request uri.
     *
     * @return string
     */
    public static function getUri()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'])['path'];

        $uri = preg_replace('/(.+)\/$/','$1',$uri);

        if (!preg_match('/^\//',$uri)) {
            $uri = "/$uri";
        }

        return $uri;
    }

    public function getRouteOptions()
    {
        return [
            'domain' => $this->getDomain() == $this->getConfig('domain')
                ? null
                : $this->getDomain()
        ];
    }

    /**
     * Get a route.
     *
     * @param string $method
     * @param string $uri
     * @return Route
     */
    public function getRoute($method,$uri)
    {
        foreach ($this->routes as $route) {
            if ($route->matches($method,$uri,$this->getRouteOptions())) {
                return $route;
            }
        }

        throw new \Exception("\"$uri\" did not match any routes.");
    }

    /**
     * Get the request method.
     *
     * @return string
     */
    public static function getMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Get the domain for the current url.
     *
     * @return string
     */
    public static function getDomain()
    {
        return $_SERVER['HTTP_HOST'];
    }

    /**
     * Load the request uri.
     *
     * @return mixed
     */
    public function load()
    {
        $uri = $this->getUri();

        $route = $this->getRoute($this->getMethod(),$uri);

        return $route->load($route->getParameters($uri));
    }
}

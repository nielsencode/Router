<?php

namespace Router;

require_once __DIR__.'/RouterException.php';
require_once __DIR__.'/Route.php';

class Router
{
    /**
     * The configuration values.
     *
     * @var array
     */
    public static $configs = [];

    /**
     * The registered routes.
     *
     * @var array
     */
    public static $routes = [];

    /**
     * Set configuration values for the router.
     *
     * @param array $configs ['domain']
     */
    public static function config($configs)
    {
        self::$configs = $configs;
    }

    /**
     * Get a configuration value.
     *
     * @param string $config
     * @return mixed
     * @throws RouterException
     */
    public static function getConfig($config)
    {
        if (!isset(self::$configs[$config])) {
            throw new RouterException("Config value \"$config\" is undefined.");
        }

        return self::$configs[$config];
    }

    /**
     * Register a route that responds to the GET method.
     *
     * @param string $route
     * @param callable $callback
     * @param array $options ['subdomain']
     */
    public static function get($route,$callback,$options=[])
    {
        self::register('get',$route,$callback,$options);
    }

    /**
     * Register a route that responds to the POST method.
     *
     * @param string $route
     * @param callable $callback
     * @param array $options
     */
    public static function post($route,$callback,$options=[])
    {
        self::register('post',$route,$callback,$options);
    }

    /**
     * Register a route.
     *
     * @param string $method
     * @param string $route
     * @param callable $callback
     * @param array $options
     */
    public static function register($method,$route,$callback,$options)
    {
        self::$routes[] = new Route($method,$route,$callback,$options);
    }

    /**
     * Get the request uri.
     *
     * @return string
     */
    public static function getUri()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'])['path'];

        if (!preg_match('/^\//',$uri)) {
            $uri = "/$uri";
        }

        return $uri;
    }

    /**
     * Get a route.
     *
     * @param string $method
     * @param string $uri
     * @return Route
     */
    public static function getRoute($method,$uri)
    {
        foreach (self::$routes as $route) {
            if ($route->matches($method,$uri)) {
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
     * Load the request uri.
     *
     * @return mixed
     */
    public static function load()
    {
        $uri = self::getUri();

        $route = self::getRoute(self::getMethod(),$uri);

        return $route->load($route->getParameters($uri));
    }

    /**
     * Get the subdomain for the current url.
     *
     * @return string
     */
    public static function getSubdomain()
    {
        return preg_replace('/\.?'.self::getConfig('domain').'/','',$_SERVER['HTTP_HOST']);
    }
}

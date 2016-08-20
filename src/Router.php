<?php

namespace Router;

require_once __DIR__.'/RouterException.php';
require_once __DIR__.'/Route.php';

class Router
{
    /**
     * The registered routes.
     *
     * @var array
     */
    public static $routes = [];

    /**
     * Register a route that responds to the GET method.
     *
     * @param string $route
     * @param callable|array $callback
     */
    public static function get($route,$callback)
    {
        self::register('get',$route,$callback);
    }

    /**
     * Register a route that responds to the POST method.
     *
     * @param string $route
     * @param callable|array $callback
     */
    public static function post($route,$callback)
    {
        self::register('post',$route,$callback);
    }

    /**
     * Register a route.
     *
     * @param string $method
     * @param string $route
     * @param callable|array $callback
     */
    public static function register($method,$route,$callback)
    {
        self::$routes[] = new Route($method,$route,$callback);
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
    public static function load()
    {
        $uri = self::getUri();

        $route = self::getRoute(self::getMethod(),$uri);

        return $route->load($route->getParameters($uri));
    }
}

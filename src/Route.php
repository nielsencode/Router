<?php

namespace Router;

class Route
{
    /**
     * Construct a new route.
     *
     * @param string $method
     * @param string $route
     * @param callable $callback
     */
    public function __construct($method,$route,$callback,$options)
    {
        $this->method = $method;

        $this->route = $route;

        $this->callback = $callback;

        $this->setOptions($options);
    }

    /**
     * Get the option defaults.
     *
     * @return array
     */
    public function getOptionDefaults()
    {
        return [
            'subdomain' => null
        ];
    }

    /**
     * Set the options.
     *
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = array_merge($this->getOptionDefaults(),$options);
    }

    /**
     * Get the regular expression for the route.
     *
     * @return string
     */
    public function getPattern()
    {
        $quoted = preg_quote($this->route,"/");

        $replaced = preg_replace('/\\\{[^\}]+\\\}/','([^\/]+)',$quoted);

        return "/^$replaced$/";
    }

    /**
     * Return whether or not the route options match a uri.
     *
     * @param string $uri
     * @return bool
     */
    public function optionsMatch($uri)
    {
        return $this->options['subdomain'] == Router::getSubdomain();
    }

    /**
     * Return whether or not the route matches a method & uri combination.
     *
     * @param string $method
     * @param string $uri
     * @return bool
     */
    public function matches($method,$uri)
    {
        if ($this->method != $method) {
            return false;
        }

        if (!preg_match($this->getPattern(),$uri)) {
            return false;
        }

        if (!$this->optionsMatch($uri)) {
            return false;
        }

        return true;
    }

    /**
     * Get the parameter values for the route.
     *
     * @param string $uri
     * @return array
     */
    public function getParameters($uri)
    {
        preg_match($this->getPattern(),$uri,$matches);

        array_shift($matches);

        return $matches;
    }

    /**
     * Load the route with parameters.
     *
     * @param array $parameters
     * @return mixed
     */
    public function load($parameters)
    {
        return call_user_func_array($this->callback,$parameters);
    }
}

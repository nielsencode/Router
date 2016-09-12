<?php

namespace Router;

class Route
{
    /**
     * Create a new Route instance.
     *
     * @param string $method
     * @param string $route
     * @param callable|array $callback
     */
    public function __construct($method,$route,$callback)
    {
        $this->method = $method;

        $this->route = $route;

        if (is_array($callback)) {
            $this->callback = array_pop($callback);

            $this->setOptions($callback);
        } else {
            $this->callback = $callback;

            $this->setOptions([]);
        }
    }

    /**
     * Get the option defaults.
     *
     * @return array
     */
    protected function getOptionDefaults()
    {
        return [
            'domain' => null
        ];
    }

    /**
     * Set the options.
     *
     * @param array $options
     */
    protected function setOptions($options)
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
     * Return whether or not the route matches a set of options.
     *
     * @param array $options
     * @return bool
     */
    public function optionsMatch($options)
    {
        return empty(array_diff_assoc($this->options,$options));
    }

    /**
     * Return whether or not the route matches the given parameters.
     *
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return bool
     */
    public function matches($method,$uri,$options)
    {
        if ($this->method != $method) {
            return false;
        }

        if (!preg_match($this->getPattern(),$uri)) {
            return false;
        }

        if (!$this->optionsMatch($options)) {
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

<?php
namespace Components\Router\Router;

class Route
{
    protected $uri;

    protected $callback;

    public function __construct($uri,$callback)
    {
        $this->uri = $uri;

        $this->callback = $callback;
    }

    protected function getPatternFromUri()
    {
        return '#^'.preg_replace('#\{[^\}]+\}#','([^/]+)',$this->uri).'$#';
    }

    public function matchesUri($uri)
    {
        return preg_match($this->getPatternFromUri(),$uri);
    }

    public function getParameters($uri)
    {
        preg_match($this->getPatternFromUri(),$uri,$matches);

        return array_slice($matches,1);
    }

    public function load($parameters)
    {
        $callback = $this->callback;

        return call_user_func_array($callback,$parameters);
    }
}

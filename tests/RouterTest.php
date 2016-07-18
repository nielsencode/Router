<?php

require_once __DIR__.'/../src/Router.php';

use PHPUnit\Framework\TestCase;
use Nielsen\Router\Router;

class RouterTest extends TestCase
{
    public static function setUpBeforeClass()
    {
        Router::get('/',function() {
            return '/';
        });

        Router::get('/rocks',function() {
            return '/rocks';
        });

        Router::post('/rocks',function() {
            return '/rocks';
        });

        Router::get('/rocks/{id}',function($id) {
            return '/rocks/{id}';
        });

        Router::get('/rocks/{id}/type',function($id) {
            return '/rocks/{id}/type';
        });
    }

    public function RouteProvider()
    {
        return [
            [
                'uri' => '/',
                'route' => '/',
                'method' => 'GET'
            ],
            [
                'uri' => '',
                'route' => '/',
                'method' => 'GET'
            ],
            [
                'uri' => '/rocks',
                'route' => '/rocks',
                'method' => 'GET'
            ],
            [
                'uri' => '/rocks',
                'route' => '/rocks',
                'method' => 'POST'
            ],
            [
                'uri' => '/rocks/1',
                'route' => '/rocks/{id}',
                'method' => 'GET'
            ],
            [
                'uri' => '/rocks/1/type',
                'route' => '/rocks/{id}/type',
                'method' => 'GET'
            ]
        ];
    }

    /**
     * @dataProvider RouteProvider
     */
    public function testRoute($uri,$route,$method)
    {
        $_SERVER['REQUEST_METHOD'] = $method;

        $this->assertEquals($route,Router::load($uri));
    }
}

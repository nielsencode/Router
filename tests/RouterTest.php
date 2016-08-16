<?php

require_once __DIR__.'/../src/Router.php';

use PHPUnit\Framework\TestCase;
use Router\Router;

class RouterTest extends TestCase
{
    public static function setUpBeforeClass()
    {
        Router::config(['domain'=>'router']);

        Router::get('/',function() {
            return '/';
        });

        Router::get('/gems',function() {
            return '/gems';
        });

        Router::get('/gems/{id}',function($id) {
            return "/gems/$id";
        });
        
        Router::get('/',function() {
            return 'subdomain';
        },['subdomain'=>'subdomain']);
    }

    public function RouteProvider()
    {
        return [
            [
                'domain' => 'router',
                'method' => 'get',
                'uri' => '/',
                'expected' => '/'
            ],
            [
                'domain' => 'router',
                'method' => 'get',
                'uri' => '',
                'expected' => '/'
            ],
            [
                'domain' => 'router',
                'method' => 'get',
                'uri' => '/gems',
                'expected' => '/gems'
            ],
            [
                'domain' => 'router',
                'method' => 'get',
                'uri' => '/gems/1',
                'expected' => '/gems/1'
            ],
            [
                'domain' => 'subdomain.router',
                'method' => 'get',
                'uri' => '/',
                'expected' => 'subdomain'
            ]
        ];
    }

    /**
     * @dataProvider RouteProvider
     */
    public function testRoute($domain,$method,$uri,$expected)
    {
        $_SERVER['HTTP_HOST'] = $domain;

        $_SERVER['REQUEST_METHOD'] = $method;

        $_SERVER['REQUEST_URI'] = $uri;

        $this->assertEquals($expected,Router::load());
    }
}

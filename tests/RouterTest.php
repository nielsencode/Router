<?php

require_once __DIR__.'/../src/Router.php';

use PHPUnit\Framework\TestCase;
use Router\Router;

class RouterTest extends TestCase
{
    public static function setUpBeforeClass()
    {
        Router::get('/',function() {
            return '/';
        });

        Router::get('/gems',function() {
            return '/gems';
        });

        Router::get('/gems/{id}',function($id) {
            return "/gems/$id";
        });
        
        Router::get('/',['domain'=>'subdomain',function() {
            return 'subdomain';
        }]);
    }

    public function RouteProvider()
    {
        return [
            [
                'method' => 'get',
                'uri' => '/',
                'expected' => '/'
            ],
            [
                'method' => 'get',
                'uri' => '',
                'expected' => '/'
            ],
            [
                'method' => 'get',
                'uri' => '/gems',
                'expected' => '/gems'
            ],
            [
                'method' => 'get',
                'uri' => '/gems/1',
                'expected' => '/gems/1'
            ],
            [
                'method' => 'get',
                'uri' => '/',
                'expected' => 'subdomain',
                'domain' => 'subdomain'
            ]
        ];
    }

    /**
     * @dataProvider RouteProvider
     */
    public function testRoute($method,$uri,$expected,$domain=null)
    {
        $_SERVER['HTTP_HOST'] = $domain;

        $_SERVER['REQUEST_METHOD'] = $method;

        $_SERVER['REQUEST_URI'] = $uri;

        $this->assertEquals($expected,Router::load());
    }
}

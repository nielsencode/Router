<?php

require_once __DIR__.'/../src/Router/Router.php';

use Components\Router\Router;

class RouterTest extends PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $router = new Router;

        $router->get('/',function() {});

        $router->get('test',function() {
            echo 'This is a test.';
        });

        $router->post('test/{id}',function($id) {
            echo "This is post test $id.";
        });

        $this->router = $router;
    }

    public function routerProvider()
    {
        return [
            [
                'GET',
                'test',
                'This is a test.'
            ],
            [
                'POST',
                'test/1',
                'This is post test 1.'
            ]
        ];
    }

    /**
     * @dataProvider routerProvider
     */
    public function testRouter($method,$route,$result)
    {
        $_SERVER['REQUEST_METHOD'] = $method;

        $_SERVER['REQUEST_URI'] = $route;

        ob_start();

        $this->router->run();

        $this->assertEquals($result,ob_get_clean());
    }
}

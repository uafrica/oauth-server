<?php

namespace OAuthServer\Test\TestCase\Middleware;

use OAuthServer\Middleware\AuthorizationEnvironmentMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class AuthorizationEnvironmentMiddlewareTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        unset($_SERVER['HTTP_AUTHORIZATION'], $_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
    }

    protected function tearDown()
    {
        unset($_SERVER['HTTP_AUTHORIZATION'], $_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
        parent::tearDown();
    }

    /**
     * @dataProvider dataSetHeaderFromEnvironment
     * @param string $env environment name
     * @return       void
     */
    public function testSetHeaderFromEnvironment($env)
    {
        $_SERVER[$env] = 'from env';

        $request = new ServerRequest();
        $response = new Response();
        $next = function (ServerRequestInterface $request, ResponseInterface $response) {
            $this->assertSame('from env', $request->getHeaderLine('authorization'));
        };

        $middleware = new AuthorizationEnvironmentMiddleware();

        $middleware($request, $response, $next);
    }

    /**
     * @return array
     */
    public function dataSetHeaderFromEnvironment()
    {
        return [
            ['HTTP_AUTHORIZATION'],
            ['REDIRECT_HTTP_AUTHORIZATION'],
        ];
    }

    public function testSetHeaderFromFirstEnvironment()
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'from authorization';
        $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] = 'from redirect authorization';

        $request = new ServerRequest();
        $response = new Response();
        $next = function (ServerRequestInterface $request, ResponseInterface $response) {
            $this->assertSame('from authorization', $request->getHeaderLine('authorization'));
        };

        $middleware = new AuthorizationEnvironmentMiddleware();

        $middleware($request, $response, $next);
    }

    public function testNotSetHeaderWhenExists()
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'from authorization';
        $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] = 'from redirect authorization';

        $request = (new ServerRequest())->withHeader('Authorization', 'from header');
        $response = new Response();
        $next = function (ServerRequestInterface $request, ResponseInterface $response) {
            $this->assertSame('from header', $request->getHeaderLine('authorization'));
        };

        $middleware = new AuthorizationEnvironmentMiddleware();

        $middleware($request, $response, $next);
    }
}

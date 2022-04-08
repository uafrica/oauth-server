<?php

namespace OAuthServer\Test\TestCase\Controller;

use Cake\Core\Plugin;
use Cake\Http\ServerRequest;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestCase;

class OpenidConfigurationControllerTest extends IntegrationTestCase
{
    public $fixtures = [
    ];

    /**
     * @noinspection PhpIncludeInspection
     */
    public function setUp()
    {
        // class Router needs to be loaded in order for TestCase to automatically include routes
        // not really sure how to do it properly, this hotfix seems good enough
        Router::defaultRouteClass();

        parent::setUp();

        Router::connect('/');
        Router::scope('/', static function (RouteBuilder $route) {
            $route->fallbacks();
        });
        include Plugin::configPath('OAuthServer') . 'routes.php';

        Router::fullBaseUrl('http://issuer.example.com');
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testAssertRoute()
    {
        $parsed = Router::parseRequest(new ServerRequest('/.well-known/openid-configuration'));
        $this->assertEquals([
            'controller' => 'OpenidConfiguration',
            'action' => 'view',
            'plugin' => 'OAuthServer',
            'pass' => [],
            '_matchedRoute' => '/.well-known/openid-configuration',
        ], $parsed);

        $parsed = Router::parseRequest(new ServerRequest('/oauth/jwks.json'));
        $this->assertEquals([
            'controller' => 'OpenidConfiguration',
            'action' => 'jwks',
            '_ext' => 'json',
            'plugin' => 'OAuthServer',
            'pass' => [],
            '_matchedRoute' => '/oauth/jwks',
        ], $parsed);
    }

    public function testGetOpenidConfiguration()
    {
        $this->get('/.well-known/openid-configuration');

        $this->assertResponseOk();

        $this->assertSame([
            'issuer' => 'http://issuer.example.com',
            'authorization_endpoint' => 'http://issuer.example.com/oauth/authorize',
            'token_endpoint' => 'http://issuer.example.com/oauth/access_token',
            'jwks_uri' => 'http://issuer.example.com/oauth/jwks.json',
            'response_types_supported' => ['code'],
            'subject_types_supported' => ['public'],
            'id_token_signing_alg_values_supported' => ['RS256'],
            'token_endpoint_auth_methods_supported' => [
                'authorization_code',
                'refresh_token',
                'client_credentials',
                'password',
            ],
        ], $this->grabResponseJson());
    }

    public function testGetJwks()
    {
        $this->get('/oauth/jwks.json');

        $this->assertResponseOk();

        $this->assertSame([
            'keys' => [
                [
                    'kid' => '26ec50b79406d68b260b2bd3bc65de4aa895ba6f9e05eb42a0653b547553121e',
                    'e' => 'AQAB',
                    'kty' => 'RSA',
                    'alg' => 'RS256',
                    'n' => 'zezLdfKQoH8OVaJ83dEfa_C8oVH_wsYo_QuGH8TkEBU-RkQqOUffDmhMznuVJNiWwlc6giad059LxTCH-BLXc7mfh6F_u4pmL7FgogyyfwG2_4ymTB2Wkc0zVU6THGDO03JusxnNfzdQ7aUt6OQHIxwaMofTfomIeXHALDuOWzVjpxi0t3ZiFUIBJKviBUip7nBCFVR8nGX7UE9_FKD-DyMrpfRUkltW7Oy_ELLAarWPCe6m3pH08wHVjYQ7VHtbmAKksWN6mS1zDbDP2ZxvPJEaWKmva8XBFZpe-xeps9zReaZa5oHa1Z4kTlR9IfGR94wgxCNMJbib1KGn7JO_UQ',
                    'use' => 'sig',
                ],
            ],
        ], $this->grabResponseJson());
    }

    private function grabResponseJson()
    {
        return json_decode($this->_getBodyAsString(), true);
    }
}

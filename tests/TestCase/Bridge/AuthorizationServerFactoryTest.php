<?php

namespace OAuthServer\Test\TestCase\Bridge;

use Cake\Core\Plugin;
use Cake\TestSuite\TestCase;
use League\OAuth2\Server\AuthorizationServer;
use OAuthServer\Bridge\AuthorizationServerFactory;

class AuthorizationServerFactoryTest extends TestCase
{
    public function testCreate()
    {
        $privateKeyPath = Plugin::path('OAuthServer') . 'tests/Fixture/test.pem';
        $key = 'def00000dc02308bae1781f846e667ca557628277485ba7c5ce897b74deb7b26ba1429a5b08d775708626a7f0664688d46f102066aefbf47e0000798043d11e06574ea83';
        chmod($privateKeyPath, 0600);

        $factory = new AuthorizationServerFactory($privateKeyPath, $key);

        $this->assertInstanceOf(AuthorizationServer::class, $factory->create());
    }
}

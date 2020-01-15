<?php

namespace OAuthServer\Test\TestCase\Bridge;

use Cake\Core\Plugin;
use Cake\TestSuite\TestCase;
use League\OAuth2\Server\ResourceServer;
use OAuthServer\Bridge\ResourceServerFactory;

class ResourceServerFactoryTest extends TestCase
{
    public function testCreate()
    {
        $publicKeyPath = Plugin::path('OAuthServer') . 'tests/Fixture/test-pub.pem';
        chmod($publicKeyPath, 0600);

        $factory = new ResourceServerFactory($publicKeyPath);

        $this->assertInstanceOf(ResourceServer::class, $factory->create());
    }
}

<?php

namespace OAuthServer\Test\TestCase\Bridge;

use Cake\Core\Plugin;
use Cake\TestSuite\TestCase;
use Defuse\Crypto\Key;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use OAuthServer\Bridge\AuthorizationServerFactory;

class AuthorizationServerFactoryTest extends TestCase
{
    /**
     * @var string
     */
    private $privateKeyPath;

    /**
     * @var string
     */
    private $key;

    public function setUp()
    {
        parent::setUp();
        $this->privateKeyPath = Plugin::path('OAuthServer') . 'tests/Fixture/test.pem';
        chmod($this->privateKeyPath, 0600);

        $this->key = 'def00000dc02308bae1781f846e667ca557628277485ba7c5ce897b74deb7b26ba1429a5b08d775708626a7f0664688d46f102066aefbf47e0000798043d11e06574ea83';
    }

    public function testCreate()
    {
        $factory = new AuthorizationServerFactory($this->privateKeyPath, $this->key);

        $this->assertInstanceOf(AuthorizationServer::class, $factory->create());
    }

    public function testSetPrivateKeyWithInstance()
    {
        $factory = new AuthorizationServerFactory($this->privateKeyPath, $this->key);

        $factory->setPrivateKey(new CryptKey($this->privateKeyPath));

        $this->assertInstanceOf(CryptKey::class, $factory->getPrivateKey());
    }

    public function testSetEncryptionKeyWithInstance()
    {
        $factory = new AuthorizationServerFactory($this->privateKeyPath, $this->key);

        $factory->setEncryptionKey(Key::createNewRandomKey());

        $this->assertInstanceOf(Key::class, $factory->getEncryptionKey());
    }
}

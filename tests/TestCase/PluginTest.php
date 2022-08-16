<?php

namespace OAuthServer\Test\TestCase\Controller;

use Cake\TestSuite\TestCase;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Grant\GrantTypeInterface;
use League\OAuth2\Server\Repositories\RepositoryInterface;
use League\OAuth2\Server\ResourceServer;
use OAuthServer\Lib\Enum\Repository;
use OAuthServer\Lib\Enum\Token;
use OAuthServer\Plugin;
use DateInterval;

/**
 * Based off the default config
 */
class PluginTest extends TestCase
{
    /**
     * @var Plugin
     */
    protected Plugin $plugin;

    /**
     * @inheritDoc
     */
    public function setUp()
    {
        parent::setUp();
        $this->plugin = new Plugin([]);
    }

    /**
     * @return void
     */
    public function testInstance(): void
    {
        $this->assertInstanceOf(Plugin::class, Plugin::instance());
    }

    /**
     * @return void
     */
    public function testGetPrivateKey(): void
    {
        $this->assertInstanceOf(CryptKey::class, $this->plugin->getPrivateKey());
    }

    /**
     * @return void
     */
    public function testGetPublicKey(): void
    {
        $this->assertInstanceOf(CryptKey::class, $this->plugin->getPublicKey());
    }

    /**
     * @return void
     */
    public function testGetEncryptionKey(): void
    {
        $this->assertInternalType('string', $this->plugin->getEncryptionKey());
    }

    /**
     * @return void
     */
    public function testGetDefaultScope(): void
    {
        $defaultScope = $this->plugin->getDefaultScope();
        $this->assertInternalType('string', $defaultScope);
        $this->assertEquals('', $defaultScope);
    }

    /**
     * @return void
     */
    public function testGetGrantObjects(): void
    {
        $grantObjects = $this->plugin->getGrantObjects();
        $this->assertInternalType('array', $grantObjects);
        foreach ($grantObjects as $grantObject) {
            $this->assertInstanceOf(GrantTypeInterface::class, $grantObject);
        }
    }

    /**
     * @return void
     */
    public function testGetAuthorizationServer(): void
    {
        $this->assertInstanceOf(AuthorizationServer::class, $this->plugin->getAuthorizationServer());
    }

    /**
     * @return void
     */
    public function testGetResourceServer(): void
    {
        $this->assertInstanceOf(ResourceServer::class, $this->plugin->getResourceServer());
    }

    /**
     * @return void
     */
    public function testGetRepository(): void
    {
        foreach (Repository::values() as $enum) {
            $this->assertInstanceOf(Repository::class, $enum);
            $this->assertInstanceOf(RepositoryInterface::class, $this->plugin->getRepository($enum));
        }
    }

    /**
     * @return void
     */
    public function testGetTokensTimeToLive(): void
    {
        $ttl = $this->plugin->getTokensTimeToLiveIntervals();
        $this->assertInternalType('array', $ttl);
        foreach (Token::toArray() as $type) {
            $this->assertArrayHasKey($type, $ttl);
            $this->assertInstanceOf(DateInterval::class, $ttl[$type]);
        }
    }

    /**
     * @return void
     */
    public function testGetStatus(): void
    {
        $status = $this->plugin->getStatus();
        $this->assertInternalType('array', $status);
        foreach (['service_status', 'grant_types', 'extensions', 'refresh_tokens_enabled', 'token_ttl_seconds'] as $key) {
            $this->assertArrayHasKey($key, $status);
        }
    }

    /**
     * @return void
     */
    public function testGetPath(): void
    {
        $this->assertInternalType('string', $this->plugin->getPath());
    }
}
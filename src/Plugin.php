<?php

namespace OAuthServer;

use Cake\Core\Configure;
use League\Event\EmitterAwareTrait;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Grant\GrantTypeInterface;
use League\OAuth2\Server\ResourceServer;
use OAuthServer\Lib\Enum\GrantType;
use OAuthServer\Lib\Factory;
use InvalidArgumentException;
use LogicException;

/**
 * OAuth 2.0 plugin object
 *
 * May construct more centrally plugin configured objects
 */
class Plugin
{
    use EmitterAwareTrait;

    /**
     * Hold the class singleton instance
     *
     * @var Plugin|null
     */
    private static ?Plugin $instance = null;

    /**
     * The object is created from within the class itself
     * only if the class has no instance
     *
     * @return Plugin|Singleton|null
     */
    public static function instance()
    {
        if (self::$instance == null) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * Get the OAuth 2.0 server private key object
     *
     * @return CryptKey
     */
    public function getPrivateKey(): CryptKey
    {
        $path     = Configure::read('OAuthServer.privateKey.path') ?? '';
        $password = Configure::read('OAuthServer.privateKey.password');
        return new CryptKey($path, $password);
    }

    /**
     * Get the OAuth 2.0 server public key object
     *
     * @return CryptKey
     */
    public function getPublicKey(): CryptKey
    {
        $path = Configure::read('OAuthServer.publicKey.path') ?? '';
        return new CryptKey($path);
    }

    /**
     * Get the OAuth 2.0 server encryption key string
     *
     * @return string
     */
    public function getEncryptionKey(): string
    {
        $key = Configure::read('OAuthServer.encryptionKey');
        if (!is_string($key)) {
            $key = (string)$key;
        }
        if (empty($key)) {
            throw new LogicException('missing encryption key');
        }
        return $key;
    }

    /**
     * Get the OAuth 2.0 server default scope string
     *
     * @return string
     */
    public function getDefaultScope(): string
    {
        return Configure::read('OAuthServer.defaultScope') ?? '';
    }

    /**
     * Get the OAuth 2.0 server enabled grant objects
     *
     * @return GrantTypeInterface[]
     * @throws InvalidArgumentException
     */
    public function getGrantObjects(): array
    {
        $configuredGrantTypes   = Configure::read('OAuthServer.grants') ?? [];
        $configuredTtl          = Configure::read('OAuthServer.ttl') ?? [];
        $configuredRepositories = Configure::read('OAuthServer.mapping') ?? [];

        $privateKey    = $this->getPrivateKey();
        $encryptionKey = $this->getEncryptionKey();
        $defaultScope  = $this->getDefaultScope();
        $emitter       = $this->getEmitter();

        $grantObjects = [];

        foreach ($configuredGrantTypes as $grantType) {
            $grantObjects[] = Factory::grantObject(
                new GrantType($grantType),
                $privateKey,
                $encryptionKey,
                $defaultScope,
                $emitter,
                $configuredTtl,
                $configuredRepositories
            );
        }

        return $grantObjects;
    }

    /**
     * @return AuthorizationServer
     */
    public function getAuthorizationServer(): AuthorizationServer
    {
        $configuredRepositories = Configure::read('OAuthServer.mapping') ?? [];
        $privateKey             = $this->getPrivateKey();
        $encryptionKey          = $this->getEncryptionKey();
        $server                 = Factory::authorizationServer($privateKey, $encryptionKey, $configuredRepositories);
        foreach ($this->getGrantObjects() as $grantObject) {
            $server->enableGrantType($grantObject);
        }
        $server->setEmitter($this->getEmitter());
        return $server;
    }

    /**
     * @return ResourceServer
     */
    public function getResourceServer(): ResourceServer
    {
        $configuredRepositories = Configure::read('OAuthServer.mapping') ?? [];
        $publicKey              = $this->getPublicKey();
        return Factory::resourceServer($publicKey, $configuredRepositories);
    }
}
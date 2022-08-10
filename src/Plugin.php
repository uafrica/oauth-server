<?php

namespace OAuthServer;

use Cake\Core\Plugin as CakePlugin;
use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Datasource\RepositoryInterface;
use League\Event\EmitterAwareTrait;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Grant\GrantTypeInterface;
use League\OAuth2\Server\ResourceServer;
use OAuthServer\Lib\Enum\GrantType;
use OAuthServer\Lib\Enum\Repository;
use OAuthServer\Lib\Factory;
use InvalidArgumentException;
use LogicException;
use Exception;

/**
 * OAuth 2.0 plugin object
 *
 * May construct more centrally plugin configured objects
 */
class Plugin extends BasePlugin
{
    use EmitterAwareTrait;

    /**
     * Get the OAuth 2.0 server private key object
     *
     * @return CryptKey
     */
    public function getPrivateKey(): ?CryptKey
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
     * @throws LogicException
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
     * @throws Exception
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
     * Get the OAuth 2.0 authorization server handling object
     *
     * @return AuthorizationServer
     */
    public function getAuthorizationServer(): AuthorizationServer
    {
        $configuredRepositories  = Configure::read('OAuthServer.mapping') ?? [];
        $configuredRefreshTokens = Configure::read('OAuthServer.refreshTokens');
        $privateKey              = $this->getPrivateKey();
        $encryptionKey           = $this->getEncryptionKey();
        $server                  = Factory::authorizationServer($privateKey, $encryptionKey, $configuredRepositories);
        foreach ($this->getGrantObjects() as $grantObject) {
            $server->enableGrantType($grantObject);
        }
        $server->setEmitter($this->getEmitter());
        $server->revokeRefreshTokens($configuredRefreshTokens ?? true);
        return $server;
    }

    /**
     * Get the OAuth 2.0 resouce server handling object
     *
     * @return ResourceServer
     * @throws Exception
     */
    public function getResourceServer(): ResourceServer
    {
        $configuredRepositories = Configure::read('OAuthServer.mapping') ?? [];
        $publicKey              = $this->getPublicKey();
        return Factory::resourceServer($publicKey, $configuredRepositories);
    }

    /**
     * Get the Cake repository representation of the given OAuth 2.0 server repository requirement
     *
     * @param Repository $repository
     * @return RepositoryInterface
     * @throws Exception
     */
    public function getRepository(Repository $repository): RepositoryInterface
    {
        $configuredRepositories = Configure::read('OAuthServer.mapping') ?? [];
        $repositories           = Factory::repositories($configuredRepositories);
        return $repositories[$repository->getValue()];
    }

    /**
     * Get the instance from the Cake application's plugin collection
     *
     * @return Plugin
     * @throws LogicException
     */
    public static function instance(): Plugin
    {
        $name = 'OAuthServer';
        if (!$plugin = CakePlugin::getCollection()->get($name)) {
            throw new LogicException(sprintf('plugin %s not loaded', $name));
        }
        return $plugin;
    }
}
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
use DateInterval;
use InvalidArgumentException;
use LogicException;
use Exception;
use function Functional\map;

/**
 * OAuth 2.0 plugin object
 *
 * May construct more centrally plugin configured objects
 */
class Plugin extends BasePlugin
{
    use EmitterAwareTrait;

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
        $configuredRepositories = Configure::read('OAuthServer.repositories') ?? [];

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
        $configuredRepositories  = Configure::read('OAuthServer.repositories') ?? [];
        $configuredRefreshTokens = Configure::read('OAuthServer.refreshTokensEnabled');
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
        $configuredRepositories = Configure::read('OAuthServer.repositories') ?? [];
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
        $configuredRepositories = Configure::read('OAuthServer.repositories') ?? [];
        $repositories           = Factory::repositories($configuredRepositories);
        return $repositories[$repository->getValue()];
    }

    /**
     * Get the token time to live DateInterval objects by token type enum key
     *
     * @return DateInterval[] e.g. [Token::ACCESS_TOKEN => Object(DateInterval), ...]
     * @throws InvalidArgumentException
     */
    public function getTokensTimeToLiveIntervals(): array
    {
        $mapping = Configure::read('OAuthServer.ttl') ?? [];
        return Factory::timeToLiveIntervals($mapping);
    }

    /**
     * Get status parameters
     *
     *   service_status: 'disabled' or 'enabled'
     *   grant_types: ['authorization_code']
     *   extensions: ['openid_connect']
     *   refresh_tokens_enabled: true or false
     *   token_ttl_seconds: ['access_token': 86400, 'refresh_token': 86400, ...]
     *
     * @return array
     */
    public function getStatus(): array
    {
        $status = [];
        if ($clientRegistrationUrl = Configure::read('OAuthServer.clientRegistrationUrl')) {
            $status['client_registration_url'] = $clientRegistrationUrl;
        }
        $status['service_status']         = Configure::read('OAuthServer.serviceDisabled') ? 'disabled' : 'enabled';
        $status['grant_types']            = map(Plugin::instance()->getGrantObjects(), fn(GrantTypeInterface $grant) => $grant->getIdentifier());
        $status['extensions']             = [];
        $status['refresh_tokens_enabled'] = !!Configure::read('OAuthServer.refreshTokensEnabled');
        $ttl                              = Plugin::instance()->getTokensTimeToLiveIntervals();
        $status['token_ttl_seconds']      = map($ttl, fn(DateInterval $interval) => Factory::intervalTimestamp($interval));
        return $status;
    }

    /**
     * @inheritDoc
     */
    public function getPath()
    {
        // @TODO for some reason path is not giving back trailing slash so add it back here but find out why sometime
        return rtrim(parent::getPath(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }
}
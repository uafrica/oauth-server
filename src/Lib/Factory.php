<?php

namespace OAuthServer\Lib;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use Cake\Utility\Text;
use League\Event\EmitterInterface;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\AuthorizationValidators\AuthorizationValidatorInterface;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Grant\AbstractGrant;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\GrantTypeInterface;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\Repositories\RepositoryInterface;
use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use OAuthServer\Exception\Exception;
use OAuthServer\Lib\Enum\GrantType;
use OAuthServer\Lib\Enum\Repository;
use InvalidArgumentException;
use DateInterval;
use OAuthServer\Lib\Enum\Token;
use function Functional\map;

/**
 * OAuth 2.0 object factory
 *
 * May construct more generally OAuth 2.0 related objects
 */
class Factory
{
    /**
     * Creates a new unique client ID
     *
     * @return string e.g. NGYcZDRjODcxYzFkY2Rk (seems popular format)
     */
    public static function clientId(): string
    {
        return base64_encode(uniqid() . substr(uniqid(), 11, 2));
    }

    /**
     * Creates a new unique client secret
     *
     * @return string
     */
    public static function clientSecret(): string
    {
        return Security::hash(Text::uuid(), 'sha1', true);
    }

    /**
     * Get OAuth 2.0 time to live DateInterval object
     *
     * @param string|DateInterval $duration e.g. 'P1M' (every 1 month)
     * @return DateInterval
     * @throws InvalidArgumentException
     */
    public static function dateInterval($duration): DateInterval
    {
        if ($duration instanceof DateInterval) {
            return $duration;
        }
        if (!is_string($duration)) {
            throw new InvalidArgumentException('expected string duration');
        }
        try {
            return new DateInterval($duration);
        } catch (Exception $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get OAuth 2.0 time to live DateInterval objects based
     * on an array with interval specification strings
     *
     * @param array $durations e.g. [Token::ACCESS_TOKEN => 'P1M']
     * @return DateInterval[] e.g. [Token::ACCESS_TOKEN => Object(DateInterval)]
     * @throws InvalidArgumentException
     */
    public static function timeToLiveIntervals(array $durations): array
    {
        $types     = Token::rawValues();
        $defaults  = array_fill_keys($types, 'P1D');
        $durations += $defaults; // replenish mapping from defaults
        $durations = array_intersect_key($durations, $defaults); // only keys from defaults
        return map($durations, fn($duration) => static::dateInterval($duration));
    }

    /**
     * Get OAuth 2.0 required repository objects
     *
     * @param string[] $mapping e.g. [Repository::AUTH_CODE => 'MyPlugin.MyTable']
     * @return RepositoryInterface[] e.g. [Repository::AUTH_CODE => Object(RepositoryInterface implementation) {}]
     * @throws Exception
     */
    public static function repositories(array $mapping): array
    {
        $locator  = TableRegistry::getTableLocator();
        $defaults = Repository::aliasDefaults();
        $mapping  += $defaults; // replenish mapping from defaults
        $mapping  = array_intersect_key($mapping, $defaults); // only keys from defaults
        $objects  = [];
        foreach ($mapping as $type => $alias) {
            foreach ([$alias, $defaults[$type]] as $alias) {
                if ($object = $locator->get($alias)) {
                    break;
                }
            }
            if (!$object) {
                $label = Repository::labels($type);
                $msg   = sprintf('missing repository "%s" both from mapping and defaults', $label);
                throw new Exception($msg);
            }
            $objects[$type] = $object;
        }
        return $objects;
    }

    /**
     * Get OAuth 2.0 grant object of given type
     *
     * @param GrantType        $grantType
     * @param CryptKey         $privateKey
     * @param EmitterInterface $emitter
     * @param string           $encryptionKey     e.g. 'lxZFUEsBCJ2Yb14IF2ygAHI5N4+ZAUXXaSeeJm6+twsUmIen'
     * @param string           $defaultScope      e.g. 'defaultscopename1 defaultscopename2'
     * @param array            $ttlMapping        e.g. [Token::ACCESS_TOKEN => 'P1D', ...]
     * @param array            $repositoryMapping e.g. [Repository::AUTH_CODE => 'MyPlugin.MyTable', ...]
     * @return GrantTypeInterface
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public static function grantObject(
        GrantType $grantType,
        CryptKey $privateKey,
        string $encryptionKey,
        string $defaultScope,
        EmitterInterface $emitter,
        array $ttlMapping,
        array $repositoryMapping
    ): GrantTypeInterface {
        $ttl          = static::timeToLiveIntervals($ttlMapping);
        $repositories = static::repositories($repositoryMapping);
        /** @var AbstractGrant $grantObject */
        switch ($grantType->getValue()) {
            case GrantType::AUTHORIZATION_CODE:
                $grantObject = new AuthCodeGrant(
                    $repositories[Repository::AUTH_CODE],
                    $repositories[Repository::REFRESH_TOKEN],
                    $ttlMapping[Token::AUTHENTICATION_TOKEN]
                );
                break;
            case GrantType::REFRESH_TOKEN:
                $grantObject = new RefreshTokenGrant($repositories[Repository::REFRESH_TOKEN]);
                break;
            case GrantType::IMPLICIT:
                $grantObject = new ImplicitGrant($ttl[Token::ACCESS_TOKEN]);
                break;
            case GrantType::PASSWORD:
                $grantObject = new PasswordGrant($repositories[Repository::USER], $repositories[Repository::REFRESH_TOKEN]);
                break;
            default:
                $grantClassName = GrantType::classNames($grantType->getValue());
                $grantObject    = new $grantClassName();
        }
        $grantObject->setPrivateKey($privateKey);
        $grantObject->setAccessTokenRepository($repositories[Repository::ACCESS_TOKEN]);
        $grantObject->setAuthCodeRepository($repositories[Repository::AUTH_CODE]);
        $grantObject->setClientRepository($repositories[Repository::CLIENT]);
        $grantObject->setScopeRepository($repositories[Repository::SCOPE]);
        $grantObject->setUserRepository($repositories[Repository::USER]);
        $grantObject->setRefreshTokenRepository($repositories[Repository::REFRESH_TOKEN]);
        $grantObject->setRefreshTokenTTL($ttl[Token::REFRESH_TOKEN]);
        $grantObject->setDefaultScope($defaultScope);
        $grantObject->setEncryptionKey($encryptionKey);
        $grantObject->setEmitter($emitter);
        return $grantObject;
    }

    /**
     * Get OAuth 2.0 authorization server with the given private key
     *
     * @param CryptKey                   $privateKey
     * @param string                     $encryptionKey     e.g. 'lxZFUEsBCJ2Yb14IF2ygAHI5N4+ZAUXXaSeeJm6+twsUmIen'
     * @param array                      $repositoryMapping e.g. [Repository::AUTH_CODE => 'MyPlugin.MyTable', ...]
     * @param ResponseTypeInterface|null $responseType
     * @return void
     */
    public static function authorizationServer(
        CryptKey $privateKey,
        string $encryptionKey,
        array $repositoryMapping,
        ResponseTypeInterface $responseType = null
    ): AuthorizationServer {
        $repositories = static::repositories($repositoryMapping);
        return new AuthorizationServer(
            $repositories[Repository::CLIENT],
            $repositories[Repository::ACCESS_TOKEN],
            $repositories[Repository::SCOPE],
            $privateKey,
            $encryptionKey,
            $responseType
        );
    }

    /**
     * Get OAuth 2.0 resource server with the given public key
     *
     * @param CryptKey                             $publicKey
     * @param array                                $repositoryMapping
     * @param AuthorizationValidatorInterface|null $authorizationValidator
     * @return ResourceServer
     * @throws Exception
     */
    public static function resourceServer(
        CryptKey $publicKey,
        array $repositoryMapping,
        ?AuthorizationValidatorInterface $authorizationValidator = null
    ): ResourceServer {
        $repositories = static::repositories($repositoryMapping);
        return new ResourceServer(
            $repositories[Repository::ACCESS_TOKEN],
            $publicKey,
            $authorizationValidator
        );
    }
}
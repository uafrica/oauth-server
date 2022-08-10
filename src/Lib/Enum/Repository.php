<?php

namespace OAuthServer\Lib\Enum;

use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use MyCLabs\Enum\Enum;
use OAuthServer\Lib\Enum\Traits\EnumTrait;

/**
 * OAuth 2.0 repository requirements enumeration
 *
 * @method static Repository ACCESS_TOKEN()
 * @method static Repository AUTH_CODE()
 * @method static Repository CLIENT()
 * @method static Repository REFRESH_TOKEN()
 * @method static Repository SCOPE()
 */
class Repository extends Enum
{
    use EnumTrait;

    const ACCESS_TOKEN  = AccessTokenRepositoryInterface::class;
    const AUTH_CODE     = AuthCodeRepositoryInterface::class;
    const CLIENT        = ClientRepositoryInterface::class;
    const REFRESH_TOKEN = RefreshTokenRepositoryInterface::class;
    const SCOPE         = ScopeRepositoryInterface::class;
    const USER          = UserRepositoryInterface::class;

    /**
     * Maps repositories to table locator alias defaults
     *
     * @param string|null $value
     * @return string|array
     */
    public static function aliasDefaults(?string $value)
    {
        $aliases = [
            static::ACCESS_TOKEN  => 'OAuthServer.AccessTokens',
            static::AUTH_CODE     => 'OAuthServer.AuthCodes',
            static::CLIENT        => 'OAuthServer.Clients',
            static::REFRESH_TOKEN => 'OAuthServer.RefreshTokens',
            static::SCOPE         => 'OAuthServer.Scopes',
            // static::USER has no default repository, up to implementation of application
        ];
        return static::enum($value, $aliases);
    }

    /**
     * Maps repositories to readable names
     *
     * @param string|null $value
     * @return string|array
     */
    public static function labels(?string $value)
    {
        $labels = [
            static::ACCESS_TOKEN  => 'Access token repository',
            static::AUTH_CODE     => 'Auth code repository',
            static::CLIENT        => 'Client repository',
            static::REFRESH_TOKEN => 'Refresh token repository',
            static::SCOPE         => 'Scope repository',
            static::USER          => 'User repository',
        ];
        return static::enum($value, $labels);
    }
}
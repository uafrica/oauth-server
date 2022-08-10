<?php

namespace OAuthServer\Lib\Enum;

use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use MyCLabs\Enum\Enum;
use OAuthServer\Lib\Enum\Traits\EnumTrait;

/**
 * OAuth 2.0 grant types enumeration
 *
 * @method static GrantType AUTHORIZATION_CODE()
 * @method static GrantType CLIENT_CREDENTIALS()
 * @method static GrantType IMPLICIT()
 * @method static GrantType PASSWORD()
 * @method static GrantType REFRESH_TOKEN()
 */
class GrantType extends Enum
{
    use EnumTrait;

    const AUTHORIZATION_CODE = 'authorization_code';
    const CLIENT_CREDENTIALS = 'client_credentials';
    const IMPLICIT           = 'implicit';
    const PASSWORD           = 'password';
    const REFRESH_TOKEN      = 'refresh_token';

    /**
     * Maps grant types to their respective responsible objects
     *
     * @param string|null $value
     * @return string|array
     */
    public static function classNames(?string $value = null)
    {
        $classNames = [
            static::AUTHORIZATION_CODE => AuthCodeGrant::class,
            static::CLIENT_CREDENTIALS => ClientCredentialsGrant::class,
            static::IMPLICIT           => ImplicitGrant::class,
            static::PASSWORD           => PasswordGrant::class,
            static::REFRESH_TOKEN      => RefreshTokenGrant::class,
        ];
        return static::enum($value, $classNames);
    }

    /**
     * Maps grant types to normal names
     *
     * @param string|null $value
     * @return string|array
     */
    public static function labels(?string $value = null)
    {
        $labels = [
            static::AUTHORIZATION_CODE => 'Authorization code',
            static::CLIENT_CREDENTIALS => 'Client credentials',
            static::IMPLICIT           => 'Implicit',
            static::PASSWORD           => 'Password',
            static::REFRESH_TOKEN      => 'Refresh token',
        ];
        return static::enum($value, $labels);
    }
}